<?php

namespace App\Controller\Api\Common;

use App\DTO\Request\Common\FileAttachmentRequestDTO;
use App\Service\Common\FileAttachmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/file-attachments', name: 'api_file_attachments_')]
class FileAttachmentController extends AbstractController
{
    public function __construct(
        private readonly FileAttachmentService $fileAttachmentService
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] FileAttachmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->fileAttachmentService->create($dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $feedback = $this->fileAttachmentService->getById($id);

        if ($feedback->hasErrors()) {
            return $this->json($feedback, Response::HTTP_NOT_FOUND);
        }

        return $this->json($feedback, Response::HTTP_OK);
    }
}
