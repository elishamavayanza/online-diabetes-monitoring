<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\FoodCategoryRequestDTO;
use App\DTO\Response\Nutrition\FoodCategoryResponseDTO;
use App\Service\Nutrition\FoodCategoryService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/food-categories')]
#[OA\Tag(name: 'Nutrition - Food Categories', description: 'Gestion des catégories d’aliments')]
class FoodCategoryController extends AbstractController
{
    public function __construct(
        private readonly FoodCategoryService $service
    ) {}

    #[Route('', name: 'api_food_categories_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une catégorie d’aliments',
        description: 'Permet d’ajouter une nouvelle catégorie pour classifier les aliments.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la catégorie d’aliments',
        content: new OA\JsonContent(
            ref: new Model(type: FoodCategoryRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Catégorie créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Catégorie créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FoodCategoryResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] FoodCategoryRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
