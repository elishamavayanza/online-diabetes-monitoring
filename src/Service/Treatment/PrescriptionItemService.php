<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\Entity\Identity\Patient;
use App\Mapper\Treatment\PrescriptionItemMapper;
use App\Repository\Identity\UserRepository;
use App\Repository\Treatment\MedicationRepository;
use App\Repository\Treatment\PrescriptionItemRepository;
use App\Repository\Treatment\PrescriptionRepository;
use App\Security\OwnershipGuardService;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PrescriptionItemService
{
    public function __construct(
        private readonly PrescriptionItemRepository $repository,
        private readonly PrescriptionRepository $prescriptionRepository,
        private readonly MedicationRepository $medicationRepository,
        private readonly PrescriptionItemMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly OwnershipGuardService $ownershipGuard,
        private readonly UserRepository $userRepository
    ) {
    }

    public function getAll(): Feedback
    {
        $items = $this->repository->findAll();
        return (new Feedback())->setData($this->mapper->mapEntitiesToResponses($items));
    }

    // Alias pour correspondre au contrôleur ->getOne()
    public function getOne(int|string $id): Feedback
    {
        return $this->getById($id);
    }

    public function getById(int|string $id): Feedback
    {
        $item = $this->repository->find($id);
        if (!$item) {
            return (new Feedback())->setErrorFlushDescription('Élément introuvable.')->autoInitFlush();
        }

        // Vérification accès via le patient lié
        $this->securityService->checkPatientAccess($item->getPrescription()->getPatient(), SecurityAction::VIEW_PRESCRIPTION);

        return (new Feedback())->setData($this->mapper->mapEntityToResponse($item));
    }

    public function getAllByPrescription(int|string $prescriptionId): Feedback
    {
        $feedback = new Feedback();
        $prescription = $this->prescriptionRepository->find($prescriptionId);

        if (!$prescription) {
            return $feedback->setErrorFlushDescription('Prescription introuvable.')->autoInitFlush();
        }

        $this->securityService->checkPatientAccess($prescription->getPatient(), SecurityAction::VIEW_PRESCRIPTION);

        $items = $this->repository->findByPrescription($prescription);

        return $feedback->setData($this->mapper->mapEntitiesToResponses($items));
    }

    /**
     * Charge les éléments de plusieurs prescriptions en une seule requête
     * (remplace le waterfall N requêtes côté client).
     *
     * @param array<int|string> $prescriptionIds
     */
    public function getAllByPrescriptionIds(int $patientId, array $prescriptionIds): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->userRepository->find($patientId);
            if (!$patient instanceof Patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PRESCRIPTION);

            $items = $this->repository->findByPrescriptionIds($prescriptionIds);

            return $feedback->setData($this->mapper->mapEntitiesToResponses($items));
        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function create(PrescriptionItemRequestDTO $dto): Feedback
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

            $medication = $this->medicationRepository->find(
                $dto->medicationId
            );

            if (!$medication) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Médicament introuvable.'
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
                SecurityAction::CREATE_PRESCRIPTION
            );

            $item = $this->mapper->mapRequestToEntity(
                $dto,
                $prescription,
                $medication,
                $this->securityService->getCurrentUser()
            );

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($item)
                )
                ->setFlushDescription(
                    'Élément de prescription ajouté avec succès.'
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

    public function update(int|string $id, PrescriptionItemRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();
        $item = $this->repository->find($id);

        if (!$item) return $feedback->setErrorFlushDescription('Introuvable.')->autoInitFlush();

        $this->securityService->checkPatientAccess($item->getPrescription()->getPatient(), SecurityAction::UPDATE_PRESCRIPTION);

        $this->ownershipGuard->assertCreator($item);

        $medication = $this->medicationRepository->find($dto->medicationId);
        if (!$medication) {
            return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
        }

        $this->mapper->mapRequestToEntity($dto, $item->getPrescription(), $medication, null, $item);

        $this->entityManager->flush();
        return $feedback->setFlushDescription('Mis à jour avec succès.')->setData($this->mapper->mapEntityToResponse($item));
    }

    public function delete(int|string $id): Feedback
    {
        $feedback = new Feedback();
        $item = $this->repository->find($id);

        if (!$item) return $feedback->setErrorFlushDescription('Introuvable.')->autoInitFlush();

        $this->securityService->checkPatientAccess($item->getPrescription()->getPatient(), SecurityAction::UPDATE_PRESCRIPTION);

        $this->ownershipGuard->assertCreator($item);

        $this->entityManager->remove($item);
        $this->entityManager->flush();

        return $feedback->setFlushDescription('Supprimé avec succès.')->autoInitFlush();
    }
}
