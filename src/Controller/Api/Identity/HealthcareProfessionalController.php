<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\Service\Identity\HealthcareProfessionalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/professionals')]
class HealthcareProfessionalController extends AbstractController
{
    public function __construct(
        private readonly HealthcareProfessionalService $professionalService
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] HealthcareProfessionalRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->professionalService->create($dto);

        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getById(string $id): JsonResponse
    {
        $feedback = $this->professionalService->getById($id);

        $status = $feedback->hasError() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
