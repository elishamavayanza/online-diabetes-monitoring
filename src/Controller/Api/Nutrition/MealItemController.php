<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\DTO\Response\Nutrition\MealItemResponseDTO;
use App\Service\Nutrition\MealItemService;
use Nelmio\ApiDocBundle\Attribute\Model;
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

    #[Route('/{id}', name: 'api_meal_items_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer un élément de repas spécifique.',
        summary: 'Afficher un élément de repas'
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MealItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Élément introuvable ou erreur')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function get(int $id): JsonResponse
    {
        $feedback = $this->service->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/patient/{patientId}', name: 'api_meal_items_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet à un médecin de récupérer tous les éléments de repas d’un patient auquel il est affecté.',
        summary: 'Voir tous les éléments de repas d\'un patient'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des éléments récupérée',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MealItemResponseDTO::class))
        )
    )]
    public function getByPatient(int $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_FORBIDDEN : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_meal_items_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’associer un aliment spécifique (avec sa portion en grammes) à un repas existant.',
        summary: 'Ajouter un aliment à un repas'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’élément de repas',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MealItemRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: MealItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MealItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_meal_items_update', methods: ['PUT'])]
    #[OA\Put(
        description: 'Permet de mettre à jour un élément de repas existant.',
        summary: 'Mettre à jour un élément de repas'
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de l’élément de repas',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MealItemRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément de repas mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MealItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides ou introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(int $id, #[MapRequestPayload] MealItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_meal_items_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un élément de repas.',
        summary: 'Supprimer un élément de repas'
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément de repas supprimé avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Élément introuvable ou erreur')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
