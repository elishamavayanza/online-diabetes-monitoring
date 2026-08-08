<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\PhysicalActivityMeasurementRequestDTO;
use App\Service\Medical\PhysicalActivityMeasurementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/physical-activity-measurements')]
class PhysicalActivityMeasurementController extends AbstractController
{
    public function __construct(
        private readonly PhysicalActivityMeasurementService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(string $patientId, #[MapRequestPayload] PhysicalActivityMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
