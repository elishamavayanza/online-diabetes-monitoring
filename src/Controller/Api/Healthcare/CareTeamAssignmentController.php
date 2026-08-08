<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\Service\Healthcare\CareTeamAssignmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/care-team-assignments')]
class CareTeamAssignmentController extends AbstractController
{
    public function __construct(
        private readonly CareTeamAssignmentService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] CareTeamAssignmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
