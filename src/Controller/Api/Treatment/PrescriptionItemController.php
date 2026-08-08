<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\Service\Treatment\PrescriptionItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescription-items')]
class PrescriptionItemController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionItemService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] PrescriptionItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
