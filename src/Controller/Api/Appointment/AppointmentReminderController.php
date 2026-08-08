<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentReminderRequestDTO;
use App\Service\Appointment\AppointmentReminderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointment-reminders')]
class AppointmentReminderController extends AbstractController
{
    public function __construct(
        private readonly AppointmentReminderService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] AppointmentReminderRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
