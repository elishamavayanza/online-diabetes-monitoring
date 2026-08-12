<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\DTO\Response\Patient\AllergyResponseDTO;
use App\Service\Patient\AllergyService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/allergies')]
#[OA\Tag(name: 'Patient - Allergies', description: 'Gestion des allergies des patients')]
class AllergyController extends AbstractController
{
    public function __construct(
        private readonly AllergyService $allergyService
    ) {}

    #[Route('', name: 'api_allergies_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une allergie',
        description: 'Permet d’enregistrer une nouvelle allergie pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’allergie',
        content: new OA\JsonContent(
            ref: new Model(type: AllergyRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Allergie créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Allergie créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: AllergyResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] AllergyRequestDTO $dto): JsonResponse
    {
        $feedback = $this->allergyService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
