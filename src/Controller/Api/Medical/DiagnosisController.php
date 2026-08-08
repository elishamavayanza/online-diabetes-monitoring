<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\Service\Medical\DiagnosisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnoses')]
class DiagnosisController extends AbstractController
{
    public function __construct(
        private readonly DiagnosisService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] DiagnosisRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
