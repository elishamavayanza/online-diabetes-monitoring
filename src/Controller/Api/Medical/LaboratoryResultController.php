<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\Service\Medical\LaboratoryResultService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/laboratory-results')]
class LaboratoryResultController extends AbstractController
{
    public function __construct(
        private readonly LaboratoryResultService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(string $patientId, #[MapRequestPayload] LaboratoryResultRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
