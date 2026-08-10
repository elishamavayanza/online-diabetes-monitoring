<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\MealRequestDTO;
use App\Service\Nutrition\MealService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/meals')]
#[OA\Tag(name: 'Nutrition - Meals', description: 'Gestion des repas')]
class MealController extends AbstractController
{
    public function __construct(
        private readonly MealService $service
    ) {}

    #[Route('', name: 'api_meals_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un repas',
        description: 'Permet d’enregistrer un nouveau repas (ex: Déjeuner, Dîner) avec ses informations descriptives.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du repas',
        content: new OA\JsonContent(
            required: ['name', 'mealType'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Déjeuner équilibré', description: 'Nom du repas'),
                new OA\Property(property: 'description', type: 'string', maxLength: 5000, nullable: true, example: 'Repas riche en protéines et légumes.', description: 'Description détaillée du repas'),
                new OA\Property(property: 'mealType', type: 'string', example: 'LUNCH', description: 'Type de repas (ex: BREAKFAST, LUNCH, DINNER, SNACK)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Repas créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Repas créé avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/MealResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MealRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
