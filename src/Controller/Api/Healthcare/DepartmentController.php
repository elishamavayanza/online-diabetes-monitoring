<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\DepartmentRequestDTO;
use App\Service\Healthcare\DepartmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/departments')]
class DepartmentController extends AbstractController
{
    public function __construct(
        private readonly DepartmentService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] DepartmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
