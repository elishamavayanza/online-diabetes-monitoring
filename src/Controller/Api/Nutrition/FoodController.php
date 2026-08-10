<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\Service\Nutrition\FoodService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/foods')]
#[OA\Tag(name: 'Nutrition - Foods', description: 'Gestion des aliments et de leurs apports nutritionnels')]
class FoodController extends AbstractController
{
    public function __construct(
        private readonly FoodService $service
    ) {}

    #[Route('', name: 'api_foods_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un aliment',
        description: 'Permet d’ajouter un nouvel aliment avec ses valeurs nutritionnelles pour 100g.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’aliment',
        content: new OA\JsonContent(
            required: ['categoryId', 'name', 'caloriesPer100g', 'carbsPer100g', 'proteinPer100g', 'fatPer100g'],
            properties: [
                new OA\Property(property: 'categoryId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de la catégorie d’aliments'),
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Pomme', description: 'Nom de l’aliment'),
                new OA\Property(property: 'description', type: 'string', maxLength: 5000, nullable: true, example: 'Fruit frais croquant.', description: 'Description de l’aliment'),
                new OA\Property(property: 'photoUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://example.com/images/apple.jpg', description: 'URL de l’image'),
                new OA\Property(property: 'caloriesPer100g', type: 'string', example: '52.00', description: 'Calories pour 100g (kcal)'),
                new OA\Property(property: 'carbsPer100g', type: 'string', example: '14.00', description: 'Glucides pour 100g (g)'),
                new OA\Property(property: 'proteinPer100g', type: 'string', example: '0.30', description: 'Protéines pour 100g (g)'),
                new OA\Property(property: 'fatPer100g', type: 'string', example: '0.20', description: 'Lipides pour 100g (g)'),
                new OA\Property(property: 'createdById', type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’utilisateur créateur (optionnel)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Aliment créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Aliment créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Nutrition\FoodResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] FoodRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
