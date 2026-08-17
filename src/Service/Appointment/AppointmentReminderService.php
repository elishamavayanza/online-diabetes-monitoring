<?php

namespace App\Service\Appointment;

use App\DTO\Feedback;
use App\DTO\Request\Appointment\AppointmentReminderRequestDTO;
use App\Mapper\Appointment\AppointmentReminderMapper;
use App\Repository\Appointment\AppointmentReminderRepository;
use App\Repository\Appointment\AppointmentRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AppointmentReminderService
{
    public function __construct(
        private readonly AppointmentReminderRepository $repository,
        private readonly AppointmentRepository $appointmentRepository,
        private readonly AppointmentReminderMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function getById(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $reminder = $this->repository->find($id);
            if (!$reminder) {
                return $feedback->setErrorFlushDescription("Rappel de rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess(
                $reminder->getAppointment()->getOrganization(),
                SecurityAction::VIEW_APPOINTMENT_REMINDER
            );

            $feedback->setData($this->mapper->mapEntityToResponse($reminder))
                ->setFlushDescription("Rappel récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByAppointment(int $appointmentId): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->appointmentRepository->find($appointmentId);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess(
                $appointment->getOrganization(),
                SecurityAction::VIEW_APPOINTMENT_REMINDER
            );

            $reminders = $this->repository->findByAppointment($appointment);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $reminders);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des rappels récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(AppointmentReminderRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $appointment = $this->appointmentRepository->find($dto->appointmentId);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess(
                $appointment->getOrganization(),
                SecurityAction::CREATE_APPOINTMENT_REMINDER
            );

            $reminder = $this->mapper->mapRequestToEntity($dto, $appointment);

            $this->entityManager->persist($reminder);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($reminder))
                ->setFlushDescription("Rappel de rendez-vous créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(int $id, AppointmentReminderRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();
        try {
            $reminder = $this->repository->find($id);
            if (!$reminder) {
                return $feedback->setErrorFlushDescription("Rappel de rendez-vous introuvable.")->autoInitFlush();
            }

            $appointment = $this->appointmentRepository->find($dto->appointmentId);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous associé introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess(
                $appointment->getOrganization(),
                SecurityAction::UPDATE_APPOINTMENT_REMINDER
            );

            $reminder = $this->mapper->mapRequestToEntity($dto, $appointment, $reminder);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($reminder))
                ->setFlushDescription("Rappel mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $reminder = $this->repository->find($id);
            if (!$reminder) {
                return $feedback->setErrorFlushDescription("Rappel de rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess(
                $reminder->getAppointment()->getOrganization(),
                SecurityAction::DELETE_APPOINTMENT_REMINDER
            );

            $this->entityManager->remove($reminder);
            $this->entityManager->flush();

            $feedback->setData(null)
                ->setFlushDescription("Rappel supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
