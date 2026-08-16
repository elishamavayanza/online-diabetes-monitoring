<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\BloodGlucoseMeasurementRequestDTO;
use App\Mapper\Medical\BloodGlucoseMeasurementMapper;
use App\Repository\Medical\BloodGlucoseMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BloodGlucoseMeasurementService
{
    public function __construct(
        private readonly BloodGlucoseMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly BloodGlucoseMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function index(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_GLUCOSE);

            $measurements = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $measurements);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des mesures récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function show(string $patientId, string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_GLUCOSE);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de glycémie introuvable.")->autoInitFlush();
            }

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(string $patientId, BloodGlucoseMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_GLUCOSE);

            $measurement = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure de glycémie enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $patientId, string $id, BloodGlucoseMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_GLUCOSE);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de glycémie introuvable.")->autoInitFlush();
            }

            $this->mapper->mapRequestToEntity($dto, $patient, $measurement);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure de glycémie mise à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $patientId, string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_GLUCOSE);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de glycémie introuvable.")->autoInitFlush();
            }

            $this->entityManager->remove($measurement);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Mesure de glycémie supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
