<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\Service\Appointment\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointments')]
class AppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] AppointmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
