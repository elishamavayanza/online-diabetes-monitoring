<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\Entity\Treatment\MedicationIntake;
use App\Mapper\Treatment\MedicationIntakeMapper;
use App\Repository\Treatment\MedicationIntakeRepository;
use App\Repository\Treatment\PrescriptionItemRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MedicationIntakeService
{
    public function __construct(
        private readonly MedicationIntakeRepository $repository,
        private readonly PrescriptionItemRepository $prescriptionItemRepository,
        private readonly MedicationIntakeMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $intakes = $this->repository->findAll();
            $data = array_map(fn(MedicationIntake $intake) => $this->mapper->mapEntityToResponse($intake), $intakes);

            return $feedback
                ->setData($data)
                ->setFlushDescription('Liste des prises de médicaments récupérée avec succès.')
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
            $intake = $this->repository->find($id);

            if (!$intake) {
                return $feedback
                    ->setErrorFlushDescription('Prise de médicament introuvable.')
                    ->autoInitFlush();
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($intake))
                ->setFlushDescription('Prise de médicament récupérée avec succès.')
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function create(MedicationIntakeRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $prescriptionItem = $this->prescriptionItemRepository->find(
                $dto->prescriptionItemId
            );

            if (!$prescriptionItem) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Élément de prescription introuvable.'
                    )
                    ->autoInitFlush();
            }

            $patient = $prescriptionItem
                ->getPrescription()
                ?->getPatient();

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Patient associé à la prescription introuvable.'
                    )
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::RECORD_MEDICATION_INTAKE
            );

            $intake = $this->mapper->mapRequestToEntity(
                $dto,
                $prescriptionItem,
                $patient,
                $this->securityService->getCurrentUser()
            );

            $this->entityManager->persist($intake);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($intake)
                )
                ->setFlushDescription(
                    'Prise de médicament enregistrée avec succès.'
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
    public function update(int $id, MedicationIntakeRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $intake = $this->repository->find($id);

            if (!$intake) {
                return $feedback
                    ->setErrorFlushDescription('Prise de médicament introuvable.')
                    ->autoInitFlush();
            }

            // Vérification des accès (ex: accès professionnel ou patient propriétaire)
            $this->securityService->checkProfessionalAccess(
                SecurityAction::UPDATE
            );

            $prescriptionItem = $this->prescriptionItemRepository->find(
                $dto->prescriptionItemId
            );

            if (!$prescriptionItem) {
                return $feedback
                    ->setErrorFlushDescription('Élément de prescription introuvable.')
                    ->autoInitFlush();
            }

            $intake = $this->mapper->mapRequestToEntity(
                $dto,
                $prescriptionItem,
                $intake->getPatient(),
                $this->securityService->getCurrentUser(),
                $intake
            );

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($intake))
                ->setFlushDescription('Prise de médicament mise à jour avec succès.')
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
            // Utilisation de checkProfessionalAccess car checkAccess n'existe pas dans le service
            $this->securityService->checkProfessionalAccess(
                SecurityAction::DELETE_MEDICATION_INTAKE
            );

            $intake = $this->repository->find($id);

            if (!$intake) {
                return $feedback
                    ->setErrorFlushDescription('Prise de médicament introuvable.')
                    ->autoInitFlush();
            }

            $this->entityManager->remove($intake);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Prise de médicament supprimée avec succès par le clinicien.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : Seul un clinicien peut supprimer une prise. ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }
}
