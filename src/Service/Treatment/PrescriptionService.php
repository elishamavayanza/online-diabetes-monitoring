<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\PrescriptionRequestDTO;
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
