<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\DTO\Response\Healthcare\CareTeamAssignmentResponseDTO;
use App\Service\Healthcare\CareTeamAssignmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ref: new Model(type: CareTeamAssignmentRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: CareTeamAssignmentResponseDTO::class))
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
