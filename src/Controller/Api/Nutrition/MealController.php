<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\MealRequestDTO;
use App\Service\Nutrition\MealService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/meals')]
class MealController extends AbstractController
{
    public function __construct(
        private readonly MealService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] MealRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
