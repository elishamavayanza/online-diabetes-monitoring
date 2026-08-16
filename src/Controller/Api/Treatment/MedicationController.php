<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\DTO\Response\Treatment\MedicationResponseDTO;
use App\Service\Treatment\MedicationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medications')]
#[OA\Tag(name: 'Treatment - Medications', description: 'Gestion des médicaments')]
class MedicationController extends AbstractController
{
    public function __construct(
        private readonly MedicationService $service
    ) {}

    #[Route('', name: 'api_medications_all', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les médicaments du catalogue.',
        summary: 'Lister les médicaments'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MedicationResponseDTO::class))
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function all(): JsonResponse
    {
        $feedback = $this->service->all();
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medications_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’un médicament spécifique.',
        summary: 'Afficher un médicament'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique du médicament', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Médicament récupéré avec succès',
        content: new OA\JsonContent(ref: new Model(type: MedicationResponseDTO::class))
    )]
    #[OA\Response(response: 404, description: 'Médicament non trouvé')]
    public function get(string $id): JsonResponse
    {
        $feedback = $this->service->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_medications_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer un nouveau médicament dans le catalogue.',
        summary: 'Créer un médicament'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du médicament',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicationRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Médicament créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Médicament créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MedicationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medications_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier un médicament existant.',
        summary: 'Mettre à jour un médicament'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique du médicament', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MedicationRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Médicament mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Médicament non trouvé')]
    public function update(string $id, #[MapRequestPayload] MedicationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medications_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un médicament du catalogue.',
        summary: 'Supprimer un médicament'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique du médicament', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Médicament supprimé avec succès')]
    #[OA\Response(response: 404, description: 'Médicament non trouvé')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
