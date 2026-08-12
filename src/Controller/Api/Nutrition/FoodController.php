<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\DTO\Response\Nutrition\FoodResponseDTO;
use App\Service\Nutrition\FoodService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ref: new Model(type: FoodRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: FoodResponseDTO::class))
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
