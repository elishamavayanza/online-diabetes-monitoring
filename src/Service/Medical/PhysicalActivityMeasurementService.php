<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\PhysicalActivityMeasurementRequestDTO;
use App\Mapper\Medical\PhysicalActivityMeasurementMapper;
use App\Repository\Medical\PhysicalActivityMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PhysicalActivityMeasurementService
{
    public function __construct(
        private readonly PhysicalActivityMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly PhysicalActivityMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

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
            $responseDTOs = array_map(fn($m) => $this->mapper->mapEntityToResponse($m), $measurements);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Mesures d'activité physique récupérées avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $patientId, string $measurementId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_MEASUREMENTS);

            $measurement = $this->repository->find($measurementId);
            if (!$measurement || $measurement->getPatient()->getId() !== $patient->getId()) {
                return $feedback->setErrorFlushDescription("Mesure d'activité physique introuvable pour ce patient.")->autoInitFlush();
            }

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure d'activité physique récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(string $patientId, PhysicalActivityMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_ACTIVITY);

            $user = $this->securityService->getCurrentUser();
            $measurement = $this->mapper->mapRequestToEntity($dto, $patient);

            if (method_exists($measurement, 'setIssuer')) {
                $measurement->setIssuer($user);
            }

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure d'activité physique enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $patientId, string $measurementId, PhysicalActivityMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_ACTIVITY);

            $measurement = $this->repository->find($measurementId);
            if (!$measurement || $measurement->getPatient()->getId() !== $patient->getId()) {
                return $feedback->setErrorFlushDescription("Mesure d'activité physique introuvable pour ce patient.")->autoInitFlush();
            }

            $this->mapper->mapRequestToEntity($dto, $patient, $measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure d'activité physique mise à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $patientId, string $measurementId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_ACTIVITY);

            $measurement = $this->repository->find($measurementId);
            if (!$measurement || $measurement->getPatient()->getId() !== $patient->getId()) {
                return $feedback->setErrorFlushDescription("Mesure d'activité physique introuvable pour ce patient.")->autoInitFlush();
            }

            $this->entityManager->remove($measurement);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Mesure d'activité physique supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
