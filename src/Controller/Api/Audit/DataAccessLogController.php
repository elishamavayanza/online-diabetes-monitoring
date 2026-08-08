<?php

namespace App\Controller\Api\Audit;

use App\DTO\Request\Audit\DataAccessLogRequestDTO;
use App\Service\Audit\DataAccessLogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/data-access-logs')]
class DataAccessLogController extends AbstractController
{
    public function __construct(
        private readonly DataAccessLogService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] DataAccessLogRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
