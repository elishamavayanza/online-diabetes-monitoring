<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\PrescriptionVersionRequestDTO;
use App\Mapper\Treatment\PrescriptionVersionMapper;
use App\Repository\Identity\UserRepository;
use App\Repository\Treatment\PrescriptionRepository;
use App\Repository\Treatment\PrescriptionVersionRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrescriptionVersionService
{
    public function __construct(
        private readonly PrescriptionVersionRepository $repository,
        private readonly PrescriptionRepository $prescriptionRepository,
        private readonly UserRepository $userRepository,
        private readonly PrescriptionVersionMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function getOne(int|string $id): Feedback
    {
        $feedback = new Feedback();
        $version = $this->repository->find($id);

        if (!$version) {
            return $feedback->setErrorFlushDescription('Version de prescription introuvable.')->autoInitFlush();
        }

        $patient = $version->getPrescription()?->getPatient();
        if ($patient) {
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PRESCRIPTION);
        }

        return $feedback->setData($this->mapper->mapEntityToResponse($version));
    }

    public function getAllByPrescription(int|string $prescriptionId): Feedback
    {
        $feedback = new Feedback();
        $prescription = $this->prescriptionRepository->find($prescriptionId);

        if (!$prescription) {
            return $feedback->setErrorFlushDescription('Prescription introuvable.')->autoInitFlush();
        }

        $patient = $prescription->getPatient();
        if ($patient) {
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PRESCRIPTION);
        }

        $versions = $this->repository->findBy(['prescription' => $prescription], ['id' => 'DESC']);

        return $feedback->setData($this->mapper->mapEntitiesToResponses($versions));
    }

    public function create(PrescriptionVersionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescription = $this->prescriptionRepository->find(
                $dto->prescriptionId
            );

            if (!$prescription) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Prescription introuvable.'
                    )
                    ->autoInitFlush();
            }

            $modifiedBy = $this->userRepository->find(
                $dto->modifiedById
            );

            if (!$modifiedBy) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Utilisateur modificateur introuvable.'
                    )
                    ->autoInitFlush();
            }

            $patient = $prescription->getPatient();

            if ($patient === null) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Patient associé à la prescription introuvable.'
                    )
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::UPDATE_PRESCRIPTION
            );

            $version = $this->mapper->mapRequestToEntity(
                $dto,
                $prescription,
                $modifiedBy
            );

            $this->entityManager->persist($version);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($version)
                )
                ->setFlushDescription(
                    'Version de prescription enregistrée avec succès.'
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
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }

    public function delete(int|string $id): Feedback
    {
        $feedback = new Feedback();
        $version = $this->repository->find($id);

        if (!$version) {
            return $feedback->setErrorFlushDescription('Version introuvable.')->autoInitFlush();
        }

        $patient = $version->getPrescription()?->getPatient();
        if ($patient) {
            $this->securityService->checkPatientAccess($patient, SecurityAction::UPDATE_PRESCRIPTION);
        }

        $this->entityManager->remove($version);
        $this->entityManager->flush();

        return $feedback->setFlushDescription('Version supprimée avec succès.')->autoInitFlush();
    }
}
