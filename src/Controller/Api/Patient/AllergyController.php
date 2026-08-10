<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\Service\Patient\AllergyService;
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
            required: ['patientId', 'name', 'severity', 'diagnosedAt'],
            properties: [
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient'),
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Pénicilline', description: 'Nom de l’allergène'),
                new OA\Property(property: 'severity', type: 'string', example: 'SEVERE', description: 'Sévérité (ex: MILD, MODERATE, SEVERE)'),
                new OA\Property(property: 'reaction', type: 'string', maxLength: 5000, nullable: true, example: 'Choc anaphylactique', description: 'Description de la réaction'),
                new OA\Property(property: 'notes', type: 'string', maxLength: 5000, nullable: true, example: 'Éviter toute administration future.', description: 'Notes additionnelles'),
                new OA\Property(property: 'diagnosedAt', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date du diagnostic')
            ],
            type: 'object'
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
                new OA\Property(property: 'data', ref: '#/components/schemas/AllergyResponseDTO')
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
