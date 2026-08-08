<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\Mapper\Treatment\PrescriptionItemMapper;
use App\Repository\Treatment\MedicationRepository;
use App\Repository\Treatment\PrescriptionItemRepository;
use App\Repository\Treatment\PrescriptionRepository;
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
        private readonly SecurityServiceInterface $securityService
    ) {
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
                $medication
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
}
