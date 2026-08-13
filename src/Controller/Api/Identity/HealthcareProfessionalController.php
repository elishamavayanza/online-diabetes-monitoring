<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\DTO\Response\Identity\HealthcareProfessionalResponseDTO;
use App\Service\Identity\HealthcareProfessionalService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/professionals')]
#[OA\Tag(name: 'Identity - Professionals', description: 'Gestion des professionnels de santé')]
class HealthcareProfessionalController extends AbstractController
{
    public function __construct(
        private readonly HealthcareProfessionalService $professionalService
    ) {}

    #[Route('', name: 'api_professionals_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste de tous les professionnels de santé actifs.',
        summary: 'Lister les professionnels de santé'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des professionnels récupérée avec succès'
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function list(): JsonResponse
    {
        $feedback = $this->professionalService->getAll();

        return $this->json($feedback, Response::HTTP_OK);
    }

    #[Route('', name: 'api_professionals_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’inscrire un nouveau professionnel de santé dans le système avec ses informations personnelles et professionnelles.',
        summary: 'Créer un professionnel de santé'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du professionnel de santé',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HealthcareProfessionalRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Professionnel de santé créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Professionnel de santé créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareProfessionalResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(
        #[MapRequestPayload] HealthcareProfessionalRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->professionalService->create($dto);

        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_professionals_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet d’obtenir les détails complets d’un professionnel de santé à partir de son identifiant unique.',
        summary: 'Récupérer un professionnel de santé par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique (UUID) du professionnel',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 200,
        description: 'Professionnel de santé trouvé',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareProfessionalResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Professionnel de santé non trouvé')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getById(string $id): JsonResponse
    {
        $feedback = $this->professionalService->getById($id);

        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_professionals_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de mettre à jour les informations d’un professionnel de santé existant.',
        summary: 'Modifier un professionnel de santé'
    )]
    #[OA\Patch(
        description: 'Permet de mettre à jour les informations d’un professionnel de santé existant.',
        summary: 'Modifier partiellement un professionnel de santé'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du professionnel de santé',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HealthcareProfessionalRequestDTO::class)
        )
    )]
    #[OA\Response(response: 200, description: 'Professionnel de santé mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données de la requête invalides ou professionnel introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(
        string $id,
        #[MapRequestPayload] HealthcareProfessionalRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->professionalService->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_professionals_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer définitivement un professionnel de santé.',
        summary: 'Supprimer un professionnel de santé'
    )]
    #[OA\Response(response: 200, description: 'Professionnel de santé supprimé avec succès')]
    #[OA\Response(response: 404, description: 'Professionnel de santé non trouvé')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->professionalService->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
