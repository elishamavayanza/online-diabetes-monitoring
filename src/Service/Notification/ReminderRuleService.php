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



    public function findAll(): Feedback
    {
        $feedback = new Feedback();
        try {
            $rules = $this->repository->findAll();
            $data = array_map(fn($rule) => $this->mapper->mapEntityToResponse($rule), $rules);

            $feedback->setData($data)->setFlushDescription("Liste récupérée avec succès.")->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function find(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $rule = $this->repository->find($id);
            if (!$rule) {
                return $feedback->setErrorFlushDescription("Règle de rappel introuvable.")->autoInitFlush();
            }

            $feedback->setData($this->mapper->mapEntityToResponse($rule))
                ->setFlushDescription("Règle récupérée avec succès.")
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }
    public function create(ReminderRuleRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_REMINDER_RULE->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            if ($this->securityService->isClinician() || $this->securityService->isNutritionist()) {
                $this->securityService->checkPatientAccess($patient, SecurityAction::CREATE_REMINDER_RULE);
            } elseif ($this->securityService->isPatient()) {
                $currentUser = $this->securityService->getCurrentUser();
                if (!$this->securityService->isPatientOwner($currentUser, $patient)) {
                    throw new AccessDeniedException("Vous ne pouvez créer des règles que pour vous-même.");
                }
            }

            $rule = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($rule);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($rule))
                ->setFlushDescription("Règle de rappel créée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(int $id, ReminderRuleRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();
        try {
            $rule = $this->repository->find($id);
            if (!$rule) {
                return $feedback->setErrorFlushDescription("Règle de rappel introuvable.")->autoInitFlush();
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // Utilisation de la méthode existante dans votre mapper en lui passant l'entité $rule
            $rule = $this->mapper->mapRequestToEntity($dto, $patient, $rule);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($rule))
                ->setFlushDescription("Règle mise à jour avec succès.")
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function toggle(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $rule = $this->repository->find($id);
            if (!$rule) {
                return $feedback->setErrorFlushDescription("Règle de rappel introuvable.")->autoInitFlush();
            }

            // Inverse l'état actif/inactif (suppose l'existence de getActive() / setActive())
            $rule->setActive(!$rule->isActive());
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($rule))
                ->setFlushDescription("Statut de la règle modifié avec succès.")
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $rule = $this->repository->find($id);
            if (!$rule) {
                return $feedback->setErrorFlushDescription("Règle de rappel introuvable.")->autoInitFlush();
            }

            $this->entityManager->remove($rule);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Règle supprimée avec succès.")->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }
}
