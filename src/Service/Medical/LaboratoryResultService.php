<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\Mapper\Medical\LaboratoryResultMapper;
use App\Repository\Medical\LaboratoryResultRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LaboratoryResultService
{
    public function __construct(
        private readonly LaboratoryResultRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly LaboratoryResultMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(string $patientId, LaboratoryResultRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_PATIENT->value);

            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $result = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($result);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($result))
                ->setFlushDescription("Résultat de laboratoire enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
