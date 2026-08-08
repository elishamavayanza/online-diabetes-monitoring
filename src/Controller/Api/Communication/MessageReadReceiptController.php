<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageReadReceiptRequestDTO;
use App\Service\Communication\MessageReadReceiptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-read-receipts')]
class MessageReadReceiptController extends AbstractController
{
    public function __construct(
        private readonly MessageReadReceiptService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] MessageReadReceiptRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
