<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\HbA1cMeasurementRequestDTO;
use App\Mapper\Medical\HbA1cMeasurementMapper;
use App\Repository\Medical\HbA1cMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HbA1cMeasurementService
{
    public function __construct(
        private readonly HbA1cMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HbA1cMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(string $patientId, HbA1cMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_HBA1C);

            $measurement = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure HbA1c enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByPatient(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_MEASUREMENTS);

            $measurements = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map(
                fn($measurement) => $this->mapper->mapEntityToResponse($measurement),
                $measurements
            );

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Mesures d’HbA1c récupérées avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $patientId, int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_MEASUREMENTS);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure d’HbA1c introuvable pour ce patient.")->autoInitFlush();
            }

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure d’HbA1c récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $patientId, int $id, HbA1cMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_HBA1C);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure d’HbA1c introuvable pour ce patient.")->autoInitFlush();
            }

            $updatedMeasurement = $this->mapper->mapRequestToEntity($dto, $patient, $measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($updatedMeasurement))
                ->setFlushDescription("Mesure d’HbA1c mise à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $patientId, int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_HBA1C);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure d’HbA1c introuvable pour ce patient.")->autoInitFlush();
            }

            $this->entityManager->remove($measurement);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Mesure d’HbA1c supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
