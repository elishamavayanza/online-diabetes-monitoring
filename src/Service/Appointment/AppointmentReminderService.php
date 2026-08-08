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

    public function create(AppointmentReminderRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::CREATE_APPOINTMENT->value);

            $appointment = $this->appointmentRepository->find($dto->appointmentId);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

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
}
