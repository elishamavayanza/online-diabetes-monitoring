<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\InsulinRequestDTO;
use App\DTO\Response\Treatment\InsulinResponseDTO;
use App\Service\Treatment\InsulinService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/insulins')]
#[OA\Tag(name: 'Treatment - Insulins', description: 'Gestion des insulines')]
class InsulinController extends AbstractController
{
    public function __construct(
        private readonly InsulinService $service
    ) {}

    #[Route('', name: 'api_insulins_all', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste des insulines du catalogue.',
        summary: 'Lister les insulines'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: InsulinResponseDTO::class))
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function all(): JsonResponse
    {
        $feedback = $this->service->all();
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_insulins_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’une insuline spécifique.',
        summary: 'Afficher une insuline'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de l’insuline', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Insuline récupérée avec succès',
        content: new OA\JsonContent(ref: new Model(type: InsulinResponseDTO::class))
    )]
    #[OA\Response(response: 404, description: 'Insuline non trouvée')]
    public function get(string $id): JsonResponse
    {
        $feedback = $this->service->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_insulins_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer une nouvelle insuline rattachée à un médicament.',
        summary: 'Créer une insuline'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’insuline',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: InsulinRequestDTO::class)
        )
    )]
    #[OA\Response(response: 201, description: 'Insuline créée avec succès')]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] InsulinRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_insulins_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une insuline existante.',
        summary: 'Mettre à jour une insuline'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de l’insuline', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: InsulinRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Insuline mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Insuline non trouvée')]
    public function update(string $id, #[MapRequestPayload] InsulinRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_insulins_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une insuline du catalogue.',
        summary: 'Supprimer une insuline'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de l’insuline', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Insuline supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Insuline non trouvée')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}