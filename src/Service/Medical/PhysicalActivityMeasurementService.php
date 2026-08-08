<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\PhysicalActivityMeasurementRequestDTO;
use App\Mapper\Medical\PhysicalActivityMeasurementMapper;
use App\Repository\Medical\PhysicalActivityMeasurementRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PhysicalActivityMeasurementService
{
    public function __construct(
        private readonly PhysicalActivityMeasurementRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly PhysicalActivityMeasurementMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(string $patientId, PhysicalActivityMeasurementRequestDTO $dto): Feedback
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
                ->setFlushDescription("Mesure d'activité physique enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
