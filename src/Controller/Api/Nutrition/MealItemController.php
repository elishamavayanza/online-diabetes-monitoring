<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\Service\Nutrition\MealItemService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/meal-items')]
#[OA\Tag(name: 'Nutrition - Meal Items', description: 'Gestion des éléments composant un repas')]
class MealItemController extends AbstractController
{
    public function __construct(
        private readonly MealItemService $service
    ) {}

    #[Route('', name: 'api_meal_items_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajouter un aliment à un repas',
        description: 'Permet d’associer un aliment spécifique (avec sa portion en grammes) à un repas existant.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’élément de repas',
        content: new OA\JsonContent(
            required: ['mealId', 'foodId', 'portionGrams'],
            properties: [
                new OA\Property(property: 'mealId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du repas concerné'),
                new OA\Property(property: 'foodId', type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’aliment'),
                new OA\Property(property: 'portionGrams', type: 'string', example: '150.00', description: 'Portion en grammes (g)'),
                new OA\Property(property: 'breadUnits', type: 'string', nullable: true, example: '1.25', description: 'Unités Pain (UP) optionnelles')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Élément de repas ajouté avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément de repas ajouté avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Nutrition\MealItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MealItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
