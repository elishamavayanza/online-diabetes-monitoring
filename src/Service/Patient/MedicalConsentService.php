<?php

namespace App\Service\Patient;

use App\DTO\Feedback;
use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Mapper\Patient\MedicalConsentMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Patient\MedicalConsentRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MedicalConsentService
{
    public function __construct(
        private readonly MedicalConsentRepository $consentRepository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly MedicalConsentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MedicalConsentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_PATIENT->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
                $this->securityService->checkOrganizationAccess($organization, SecurityAction::MANAGE_ORGANIZATION);
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $consent = $this->mapper->mapRequestToEntity($dto, $patient, $organization);

            $this->entityManager->persist($consent);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($consent))
                ->setFlushDescription("Consentement médical enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
