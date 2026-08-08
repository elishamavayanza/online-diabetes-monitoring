<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageAttachmentRequestDTO;
use App\Service\Communication\MessageAttachmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-attachments')]
class MessageAttachmentController extends AbstractController
{
    public function __construct(
        private readonly MessageAttachmentService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] MessageAttachmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
