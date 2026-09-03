<?php

namespace App\Controller\Api\Nutrition;

use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\DTO\Response\Nutrition\FoodResponseDTO;
use App\Service\Nutrition\FoodService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('', name: 'api_foods_all', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste de tous les aliments.',
        summary: 'Lister les aliments'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste des aliments récupérée avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: FoodResponseDTO::class))
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

    #[Route('/{id}', name: 'api_foods_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer un aliment par son ID.',
        summary: 'Afficher un aliment'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de l’aliment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Aliment récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Aliment récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FoodResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Aliment introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/upload-photo', name: 'api_foods_upload_photo', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de téléverser une photo pour un aliment.',
        summary: 'Téléverser une photo d\'aliment'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['photo'],
                properties: [
                    new OA\Property(property: 'photo', type: 'string', format: 'binary')
                ]
            )
        )
    )]
    #[OA\Response(response: 201, description: 'Photo téléversée avec succès')]
    public function uploadPhoto(Request $request): JsonResponse
    {
        $file = $request->files->get('photo');

        if (!$file) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Le champ photo est obligatoire.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->service->uploadPhoto($file, $request);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_foods_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ajouter un nouvel aliment avec ses valeurs nutritionnelles pour 100g.',
        summary: 'Créer un aliment'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’aliment',
        required: true,
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
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_foods_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier un aliment existant.',
        summary: 'Mettre à jour un aliment'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de l’aliment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de l’aliment',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: FoodRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Aliment mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Aliment mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FoodResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Aliment introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(int $id, #[MapRequestPayload] FoodRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_foods_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un aliment.',
        summary: 'Supprimer un aliment'
    )]
    #[OA\Parameter(name: 'id', description: 'ID de l’aliment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Aliment supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Aliment supprimé avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Aliment introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
