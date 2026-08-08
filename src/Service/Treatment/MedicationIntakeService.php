<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
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
                $prescriptionItem
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
}
