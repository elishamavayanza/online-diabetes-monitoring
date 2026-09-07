<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\InsulinInjectionRequestDTO;
use App\Entity\Identity\Patient;
use App\Entity\Treatment\InsulinInjection;
use App\Mapper\Treatment\InsulinInjectionMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Treatment\InsulinInjectionRepository;
use App\Repository\Treatment\InsulinRepository;
use App\Repository\Treatment\PrescriptionItemRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InsulinInjectionService
{
    public function __construct(
        private readonly InsulinInjectionRepository $repository,
        private readonly InsulinRepository $insulinRepository,
        private readonly PrescriptionItemRepository $prescriptionItemRepository,
        private readonly PatientRepository $patientRepository,
        private readonly InsulinInjectionMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $injections = $this->repository->findAll();
            $data = array_map(fn(InsulinInjection $injection) => $this->mapper->mapEntityToResponse($injection), $injections);

            return $feedback
                ->setData($data)
                ->setFlushDescription('Liste des injections d’insuline récupérée avec succès.')
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function getById(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $injection = $this->repository->find($id);

            if (!$injection) {
                return $feedback
                    ->setErrorFlushDescription('Injection d’insuline introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $injection->getPatient(),
                SecurityAction::VIEW_INSULIN_INJECTION
            );

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($injection))
                ->setFlushDescription('Injection d’insuline récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function getByPatient(int $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::VIEW_INSULIN_INJECTION
            );

            $injections = $this->repository->findByPatient($patient);
            $data = array_map(fn(InsulinInjection $injection) => $this->mapper->mapEntityToResponse($injection), $injections);

            return $feedback
                ->setData($data)
                ->setFlushDescription('Historique des injections du patient récupéré avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function create(InsulinInjectionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescriptionItem = $this->prescriptionItemRepository->find(
                $dto->prescriptionItemId
            );

            if (!$prescriptionItem) {
                return $feedback
                    ->setErrorFlushDescription('Élément de prescription introuvable.')
                    ->autoInitFlush();
            }

            $patient = $prescriptionItem
                ->getPrescription()
                ?->getPatient();

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient associé à la prescription introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::RECORD_INSULIN_INJECTION
            );

            $insulin = $this->insulinRepository->find($dto->insulinId);

            if (!$insulin) {
                return $feedback
                    ->setErrorFlushDescription('Insuline introuvable.')
                    ->autoInitFlush();
            }

            $injection = $this->mapper->mapRequestToEntity(
                $dto,
                $prescriptionItem,
                $insulin,
                $patient,
                $this->securityService->getCurrentUser()
            );

            $this->entityManager->persist($injection);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($injection))
                ->setFlushDescription('Injection d’insuline enregistrée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function update(int $id, InsulinInjectionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $injection = $this->repository->find($id);

            if (!$injection) {
                return $feedback
                    ->setErrorFlushDescription('Injection d’insuline introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $injection->getPatient(),
                SecurityAction::RECORD_INSULIN_INJECTION
            );

            $prescriptionItem = $this->prescriptionItemRepository->find(
                $dto->prescriptionItemId
            );

            if (!$prescriptionItem) {
                return $feedback
                    ->setErrorFlushDescription('Élément de prescription introuvable.')
                    ->autoInitFlush();
            }

            $patient = $prescriptionItem
                ->getPrescription()
                ?->getPatient();

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient associé à la prescription introuvable.')
                    ->autoInitFlush();
            }

            $insulin = $this->insulinRepository->find($dto->insulinId);

            if (!$insulin) {
                return $feedback
                    ->setErrorFlushDescription('Insuline introuvable.')
                    ->autoInitFlush();
            }

            $this->throwIfPatientMismatch($injection, $patient);

            $injection = $this->mapper->mapRequestToEntity(
                $dto,
                $prescriptionItem,
                $insulin,
                $patient,
                $this->securityService->getCurrentUser(),
                $injection
            );

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($injection))
                ->setFlushDescription('Injection d’insuline mise à jour avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::DELETE_INSULIN_INJECTION
            );

            $injection = $this->repository->find($id);

            if (!$injection) {
                return $feedback
                    ->setErrorFlushDescription('Injection d’insuline introuvable.')
                    ->autoInitFlush();
            }

            $this->entityManager->remove($injection);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Injection d’insuline supprimée avec succès par le clinicien.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : Seul un clinicien peut supprimer une injection. ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    private function throwIfPatientMismatch(InsulinInjection $injection, Patient $patient): void
    {
        if ($injection->getPatient()?->getId() !== $patient->getId()) {
            throw new AccessDeniedException(
                'Impossible d’affecter l’injection à un autre patient.'
            );
        }
    }
}