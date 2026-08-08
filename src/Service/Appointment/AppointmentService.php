<?php

namespace App\Service\Appointment;

use App\DTO\Feedback;
use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\Mapper\Appointment\AppointmentMapper;
use App\Repository\Appointment\AppointmentRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Healthcare\FacilityRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly FacilityRepository $facilityRepository,
        private readonly AppointmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(AppointmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_APPOINTMENT->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $professional = $this->professionalRepository->find($dto->professionalId);
            if (!$professional) {
                return $feedback->setErrorFlushDescription("Professionnel de santé introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
            }
            $this->securityService->checkOrganizationAccess($organization, SecurityAction::CREATE_APPOINTMENT);

            $facility = null;
            if ($dto->facilityId) {
                $facility = $this->facilityRepository->find($dto->facilityId);
                if (!$facility) {
                    return $feedback->setErrorFlushDescription("Établissement (Facility) introuvable.")->autoInitFlush();
                }
            }

            $appointment = $this->mapper->mapRequestToEntity($dto, $patient, $professional, $organization, $facility);

            $this->entityManager->persist($appointment);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($appointment))
                ->setFlushDescription("Rendez-vous créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
