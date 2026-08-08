<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\BloodPressureMeasurementRequestDTO;
use App\Service\Medical\BloodPressureMeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/blood-pressure-measurements')]
class BloodPressureMeasurementController extends AbstractController
{
    public function __construct(
        private readonly BloodPressureMeasurementService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(string $patientId, #[MapRequestPayload] BloodPressureMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
