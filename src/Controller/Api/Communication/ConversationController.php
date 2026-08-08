<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\ConversationRequestDTO;
use App\Service\Communication\ConversationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversations')]
class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ConversationService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] ConversationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
