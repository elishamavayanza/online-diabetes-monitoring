<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\Mapper\Healthcare\CareTeamAssignmentMapper;
use App\Repository\Healthcare\CareTeamAssignmentRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CareTeamAssignmentService
{
    public function __construct(
        private readonly CareTeamAssignmentRepository $assignmentRepository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly CareTeamAssignmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(CareTeamAssignmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_PATIENT->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $professional = $this->professionalRepository->find($dto->professionalId);
            if (!$professional) {
                return $feedback->setErrorFlushDescription("Professionnel introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $assignment = $this->mapper->mapRequestToEntity($dto, $patient, $professional, $organization);

            $this->entityManager->persist($assignment);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($assignment))
                ->setFlushDescription("Assignation à l'équipe de soins créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
