<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\MealRequestDTO;
use App\DTO\Response\Nutrition\MealResponseDTO;
use App\Service\Nutrition\MealService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/meals')]
#[OA\Tag(name: 'Nutrition - Meals', description: 'Gestion des repas')]
class MealController extends AbstractController
{
    public function __construct(
        private readonly MealService $service
    )
    {
    }

    #[Route('', name: 'api_meals_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste des repas. Un patient ne voit que ses propres repas, un médecin/nutritionniste peut spécifier un patientId.',
        summary: 'Lister les repas'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'ID du patient (obligatoire pour les professionnels, optionnel pour le patient)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des repas récupérée avec succès'
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    public function list(Request $request): JsonResponse
    {
        $patientId = $request->query->get('patientId') ? (int)$request->query->get('patientId') : null;
        $feedback = $this->service->list($patientId);

        return $this->json($feedback, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_meals_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’un repas spécifique par son ID.',
        summary: 'Afficher un repas'
    )]
    #[OA\Response(
        response: 200,
        description: 'Repas récupéré avec succès'
    )]
    #[OA\Response(response: 404, description: 'Repas introuvable')]
    public function show(int $id): JsonResponse
    {
        $feedback = $this->service->show($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_meals_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer un nouveau repas (ex: Déjeuner, Dîner) avec ses informations descriptives.',
        summary: 'Créer un repas'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du repas',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MealRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: MealResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MealRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_meals_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier un repas existant.',
        summary: 'Mettre à jour un repas'
    )]
    #[OA\RequestBody(
        description: 'Paramètres modifiés du repas',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MealRequestDTO::class)
        )
    )]
    #[OA\Response(response: 200, description: 'Repas mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Repas introuvable')]
    public function update(int $id, #[MapRequestPayload] MealRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_meals_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un repas.',
        summary: 'Supprimer un repas'
    )]
    #[OA\Response(response: 200, description: 'Repas supprimé avec succès')]
    #[OA\Response(response: 404, description: 'Repas introuvable')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
