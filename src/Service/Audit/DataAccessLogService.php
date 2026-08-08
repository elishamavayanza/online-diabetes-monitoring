<?php

namespace App\Service\Audit;

use App\DTO\Feedback;
use App\DTO\Request\Audit\DataAccessLogRequestDTO;
use App\Mapper\Audit\DataAccessLogMapper;
use App\Repository\Audit\DataAccessLogRepository;
use App\Repository\Identity\UserRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DataAccessLogService
{
    public function __construct(
        private readonly DataAccessLogRepository $repository,
        private readonly UserRepository $userRepository,
        private readonly PatientRepository $patientRepository,
        private readonly DataAccessLogMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(DataAccessLogRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_DATA_ACCESS_LOG->value);

            $accessedBy = $this->userRepository->find($dto->accessedById);
            if (!$accessedBy) {
                return $feedback->setErrorFlushDescription("Utilisateur (accessedBy) introuvable.")->autoInitFlush();
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $log = $this->mapper->mapRequestToEntity($dto, $accessedBy, $patient);

            $this->entityManager->persist($log);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($log))
                ->setFlushDescription("Journal d'accès aux données enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
