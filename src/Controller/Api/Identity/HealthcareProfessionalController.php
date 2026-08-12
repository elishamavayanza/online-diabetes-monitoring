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

    #[Route('', name: 'api_professionals_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un professionnel de santé',
        description: 'Permet d’inscrire un nouveau professionnel de santé dans le système avec ses informations personnelles et professionnelles.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du professionnel de santé',
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

        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_professionals_get_by_id', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupérer un professionnel de santé par son ID',
        description: 'Permet d’obtenir les détails complets d’un professionnel de santé à partir de son identifiant unique.'
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

        $status = $feedback->hasError() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
