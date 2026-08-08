<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\HbA1cMeasurementRequestDTO;
use App\Mapper\Medical\HbA1cMeasurementMapper;
use App\Repository\Medical\HbA1cMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HbA1cMeasurementService
{
    public function __construct(
        private readonly HbA1cMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HbA1cMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(string $patientId, HbA1cMeasurementRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_PATIENT->value);

            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $measurement = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($measurement))
                ->setFlushDescription("Mesure HbA1c enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
