<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\Service\Patient\AllergyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/allergies')]
class AllergyController extends AbstractController
{
    public function __construct(
        private readonly AllergyService $allergyService
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] AllergyRequestDTO $dto): JsonResponse
    {
        $feedback = $this->allergyService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
