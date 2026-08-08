<?php

namespace App\Service\Patient;

use App\DTO\Feedback;
use App\DTO\Request\Patient\AllergyRequestDTO;
use App\Mapper\Patient\AllergyMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Patient\AllergyRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AllergyService
{
    public function __construct(
        private readonly AllergyRepository $allergyRepository,
        private readonly PatientRepository $patientRepository,
        private readonly AllergyMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(AllergyRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_MEDICAL_RECORD->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_MEDICAL_RECORD);

            $allergy = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($allergy);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($allergy))
                ->setFlushDescription("Allergie enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
