<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\FoodCategoryRequestDTO;
use App\Service\Nutrition\FoodCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/food-categories')]
class FoodCategoryController extends AbstractController
{
    public function __construct(
        private readonly FoodCategoryService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] FoodCategoryRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
