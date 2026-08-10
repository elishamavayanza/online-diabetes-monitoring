<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\Service\Healthcare\CareTeamAssignmentService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/care-team-assignments')]
#[OA\Tag(name: 'Healthcare - Care Team Assignments', description: 'Gestion des affectations des équipes de soins aux patients')]
class CareTeamAssignmentController extends AbstractController
{
    public function __construct(
        private readonly CareTeamAssignmentService $service
    ) {}

    #[Route('', name: 'api_care_team_assignments_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une affectation d’équipe de soins',
        description: 'Permet d’assigner un professionnel de santé et son rôle auprès d’un patient au sein d’une organisation.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’affectation de l’équipe de soins',
        content: new OA\JsonContent(
            required: ['patientId', 'professionalId', 'organizationId', 'role', 'startDate', 'active'],
            properties: [
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique du patient'),
                new OA\Property(property: 'professionalId', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique du professionnel de santé'),
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique de l’organisation de santé'),
                new OA\Property(property: 'role', type: 'string', example: 'ATTENDING_PHYSICIAN', description: 'Rôle du professionnel dans l’équipe de soins'),
                new OA\Property(property: 'startDate', type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début de l’affectation'),
                new OA\Property(property: 'endDate', type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin de l’affectation (optionnelle)'),
                new OA\Property(property: 'active', type: 'boolean', example: true, description: 'Indique si l’affectation est active')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Affectation créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Affectation créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Healthcare\CareTeamAssignmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données de la requête invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] CareTeamAssignmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
