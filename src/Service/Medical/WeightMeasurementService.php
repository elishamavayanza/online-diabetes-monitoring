<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\WeightMeasurementRequestDTO;
use App\Mapper\Medical\WeightMeasurementMapper;
use App\Repository\Medical\WeightMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class WeightMeasurementService
{
    public function __construct(
        private readonly WeightMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly WeightMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function all(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // Utilisation de RECORD_WEIGHT ou VIEW_PATIENT selon vos règles de sécurité de lecture
            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_WEIGHT);

            $measurements = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map(fn($m) => $this->mapper->mapEntityToResponse($m), $measurements);

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

    public function get(string $patientId, string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_WEIGHT);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de poids introuvable.")->autoInitFlush();
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

    public function create(string $patientId, WeightMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_WEIGHT);

            $measurement = $this->mapper->mapRequestToEntity($dto, $patient);

            // Assignation de la date et de l'utilisateur courant si non gérés dans le mapper
            if ($measurement->getMeasuredAt() === null) {
                $measurement->setMeasuredAt(new \DateTimeImmutable());
            }
            if ($measurement->getIssuer() === null) {
                $measurement->setIssuer($this->securityService->getCurrentUser());
            }

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure de poids enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $patientId, string $id, WeightMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_WEIGHT);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de poids introuvable.")->autoInitFlush();
            }

            $measurement = $this->mapper->mapRequestToEntity($dto, $patient, $measurement);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure de poids mise à jour avec succès.")
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

            $this->securityService->checkPatientAccess($patient, SecurityAction::RECORD_WEIGHT);

            $measurement = $this->repository->findOneBy(['id' => $id, 'patient' => $patient]);
            if (!$measurement) {
                return $feedback->setErrorFlushDescription("Mesure de poids introuvable.")->autoInitFlush();
            }

            $this->entityManager->remove($measurement);
            $this->entityManager->flush();

            $feedback->setData(null)
                ->setFlushDescription("Mesure de poids supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
