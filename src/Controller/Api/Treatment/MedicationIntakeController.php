<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\Service\Treatment\MedicationIntakeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medication-intakes')]
class MedicationIntakeController extends AbstractController
{
    public function __construct(
        private readonly MedicationIntakeService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] MedicationIntakeRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
