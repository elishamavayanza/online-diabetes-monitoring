<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\MedicalRecordRequestDTO;
use App\Mapper\Medical\MedicalRecordMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Medical\MedicalRecordRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

class MedicalRecordService
{
    public function __construct(
        private readonly MedicalRecordRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly MedicalRecordMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function create(MedicalRecordRequestDTO $dto): Feedback
    {
        return $this->executeSafely(function (Feedback $feedback) use ($dto) {
            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription('Organisation introuvable.')->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::CREATE_MEDICAL_RECORD);
            $this->securityService->checkPatientAccess($patient, SecurityAction::CREATE_MEDICAL_RECORD);

            $record = $this->mapper->mapRequestToEntity($dto, $patient, $organization);

            $this->entityManager->persist($record);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($record))
                ->setFlushDescription('Dossier médical créé avec succès.')
                ->autoInitFlush();
        });
    }

    public function getOne(string $id): Feedback
    {
        return $this->executeSafely(function (Feedback $feedback) use ($id) {
            $record = $this->repository->find($id);
            if (!$record) {
                return $feedback->setErrorFlushDescription('Dossier médical introuvable.')->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($record->getOrganization(), SecurityAction::VIEW_MEDICAL_RECORD);

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($record))
                ->setFlushDescription('Dossier médical récupéré avec succès.')
                ->autoInitFlush();
        });
    }

    public function update(string $id, MedicalRecordRequestDTO $dto): Feedback
    {
        return $this->executeSafely(function (Feedback $feedback) use ($id, $dto) {
            $record = $this->repository->find($id);
            if (!$record) {
                return $feedback->setErrorFlushDescription('Dossier médical introuvable.')->autoInitFlush();
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription('Organisation introuvable.')->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::UPDATE);

            $this->mapper->mapRequestToEntity($dto, $patient, $organization, $record);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($record))
                ->setFlushDescription('Dossier médical mis à jour avec succès.')
                ->autoInitFlush();
        });
    }

    public function delete(string $id): Feedback
    {
        return $this->executeSafely(function (Feedback $feedback) use ($id) {
            $record = $this->repository->find($id);
            if (!$record) {
                return $feedback->setErrorFlushDescription('Dossier médical introuvable.')->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($record->getOrganization(), SecurityAction::MANAGE_ORGANIZATION);

            $this->entityManager->remove($record);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Dossier médical supprimé avec succès.')
                ->autoInitFlush();
        });
    }

    /**
     * Factorise la gestion des erreurs et des exceptions pour éviter la duplication.
     */
    private function executeSafely(callable $callback): Feedback
    {
        $feedback = new Feedback();

        try {
            return $callback($feedback);
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }
}
