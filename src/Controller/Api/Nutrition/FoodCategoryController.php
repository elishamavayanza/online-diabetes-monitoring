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

    #[Route('', name: 'api_food_categories_all', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste de toutes les catégories d’aliments.',
        summary: 'Lister les catégories d’aliments'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste des catégories d’aliments récupérée avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: FoodCategoryResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function all(): JsonResponse
    {
        $feedback = $this->service->all();
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_food_categories_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer une catégorie d’aliment par son ID.',
        summary: 'Afficher une catégorie d’aliments'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de la catégorie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Catégorie récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Catégorie d’aliment récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FoodCategoryResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Catégorie introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_food_categories_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ajouter une nouvelle catégorie pour classifier les aliments.',
        summary: 'Créer une catégorie d’aliments'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la catégorie d’aliments',
        required: true,
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
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_food_categories_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une catégorie d’aliment existante.',
        summary: 'Mettre à jour une catégorie d’aliments'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de la catégorie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de la catégorie d’aliments',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: FoodCategoryRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Catégorie mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Catégorie d’aliment mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FoodCategoryResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Catégorie introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(int $id, #[MapRequestPayload] FoodCategoryRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_food_categories_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une catégorie d’aliment.',
        summary: 'Supprimer une catégorie d’aliments'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de la catégorie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Catégorie supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Catégorie d’aliment supprimée avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Catégorie introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
