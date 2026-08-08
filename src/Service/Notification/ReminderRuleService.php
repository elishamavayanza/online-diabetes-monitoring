<?php

namespace App\Service\Notification;

use App\DTO\Feedback;
use App\DTO\Request\Notification\ReminderRuleRequestDTO;
use App\Mapper\Notification\ReminderRuleMapper;
use App\Repository\Notification\ReminderRuleRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReminderRuleService
{
    public function __construct(
        private readonly ReminderRuleRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly ReminderRuleMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(ReminderRuleRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_NOTIFICATION->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $rule = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($rule);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($rule))
                ->setFlushDescription("Règle de rappel créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
