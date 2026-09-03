<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\PrescriptionRequestDTO;
use App\Entity\Identity\User;
use App\Entity\Treatment\PrescriptionStatus;
use App\Mapper\Treatment\PrescriptionMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Identity\UserRepository;
use App\Repository\Treatment\PrescriptionRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrescriptionService
{
    public function __construct(
        private readonly PrescriptionRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $prescriberRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly UserRepository $userRepository,
        private readonly PrescriptionMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }
    public function getById(string|int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescription = $this->repository->find($id);

            if (!$prescription) {
                return $feedback
                    ->setErrorFlushDescription('Prescription introuvable.')
                    ->autoInitFlush();
            }

            // Vérification optionnelle des accès si nécessaire
             $this->securityService->checkOrganizationAccess($prescription->getOrganization(), SecurityAction::VIEW_PRESCRIPTION);

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($prescription))
                ->setFlushDescription('Prescription récupérée avec succès.')
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function create(PrescriptionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find(
                $dto->patientId
            );

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Patient introuvable.'
                    )
                    ->autoInitFlush();
            }

            $prescriber = $this->prescriberRepository->find(
                $dto->prescriberId
            );

            if (!$prescriber) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Prescripteur introuvable.'
                    )
                    ->autoInitFlush();
            }

            $organization = $this->organizationRepository->find(
                $dto->organizationId
            );

            if (!$organization) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Organisation introuvable.'
                    )
                    ->autoInitFlush();
            }

            $validatedBy = null;

            if ($dto->validatedById) {
                $validatedBy = $this->userRepository->find(
                    $dto->validatedById
                );
            }

            $this->securityService->checkOrganizationAccess(
                $organization,
                SecurityAction::CREATE_PRESCRIPTION
            );

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::CREATE_PRESCRIPTION
            );

            $prescription = $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $prescriber,
                $organization,
                $validatedBy
            );

            $this->entityManager->persist($prescription);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($prescription)
                )
                ->setFlushDescription(
                    'Prescription créée avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine()
                )
                ->autoInitFlush();
        }
    }

    public function update(string|int $id, PrescriptionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescription = $this->repository->find($id);

            if (!$prescription) {
                return $feedback
                    ->setErrorFlushDescription('Prescription introuvable.')
                    ->autoInitFlush();
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $prescriber = $this->prescriberRepository->find($dto->prescriberId);
            if (!$prescriber) {
                return $feedback->setErrorFlushDescription('Prescripteur introuvable.')->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription('Organisation introuvable.')->autoInitFlush();
            }

            $validatedBy = null;
            if ($dto->validatedById) {
                $validatedBy = $this->userRepository->find($dto->validatedById);
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::UPDATE_PRESCRIPTION);
            $this->securityService->checkPatientAccess($patient, SecurityAction::UPDATE_PRESCRIPTION);

            // Mise à jour de l'entité existante via le mapper
            $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $prescriber,
                $organization,
                $validatedBy,
                $prescription
            );

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($prescription))
                ->setFlushDescription('Prescription mise à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function getByPatient(string|int $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PRESCRIPTION);

            $prescriptions = $this->repository->findAllByPatient($patient);

            $data = array_map(
                fn ($prescription) => $this->mapper->mapEntityToResponse($prescription),
                $prescriptions
            );

            return $feedback
                ->setData(array_values($data))
                ->setFlushDescription('Prescriptions récupérées avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
    public function stop(string|int $id, ?string $reason, User $currentUser): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescription = $this->repository->find($id);

            if (!$prescription) {
                return $feedback->setErrorFlushDescription('Prescription introuvable.')->autoInitFlush();
            }

            // 1. Vérifier si l'utilisateur connecté est le patient propriétaire de la prescription
            // (Ajustez getPatient()->getUser() ou getPatient() selon la relation exacte entre votre entité Patient et User)
            $isPatientOwner = $prescription->getPatient()->getId() === $currentUser->getId()
                || (method_exists($prescription->getPatient(), 'getUser') && $prescription->getPatient()->getUser()?->getId() === $currentUser->getId());

            if ($isPatientOwner) {
                // Vérification de sécurité pour le patient
                $this->securityService->checkPatientAccess($prescription->getPatient(), SecurityAction::VIEW_PRESCRIPTION);
            } else {
                // Sinon, c'est un professionnel de santé, on vérifie ses droits dans l'organisation
                $this->securityService->checkOrganizationAccess($prescription->getOrganization(), SecurityAction::CANCEL_PRESCRIPTION);
            }

            // 2. Vérifier que la prescription est active
            if ($prescription->getStatus() !== PrescriptionStatus::ACTIVE) {
                return $feedback->setErrorFlushDescription('Cette prescription n\'est pas active.')->autoInitFlush();
            }

            // 3. Marquer comme annulée/arrêtée
            $prescription->setStatus(PrescriptionStatus::CANCELLED);

            // Si vous avez ajouté les champs stoppedAt et stopReason, utilisez-les de préférence :
            if (method_exists($prescription, 'setStoppedAt')) {
                $prescription->setStoppedAt(new \DateTimeImmutable());
            }
            if (method_exists($prescription, 'setStopReason')) {
                $prescription->setStopReason($reason);
            } else {
                // Fallback sur notes si les champs n'existent pas encore
                $prescription->setNotes($reason ?: 'Traitement arrêté');
            }

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($prescription))
                ->setFlushDescription('Traitement arrêté avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine())->autoInitFlush();
        }
    }

    public function delete(string|int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescription = $this->repository->find($id);

            if (!$prescription) {
                return $feedback
                    ->setErrorFlushDescription('Prescription introuvable.')
                    ->autoInitFlush();
            }

            // Utilisation de CANCEL_PRESCRIPTION (ou SECURITYACTION::DELETE)
            $this->securityService->checkOrganizationAccess(
                $prescription->getOrganization(),
                SecurityAction::CANCEL_PRESCRIPTION
            );

            $this->entityManager->remove($prescription);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Prescription supprimée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
}
