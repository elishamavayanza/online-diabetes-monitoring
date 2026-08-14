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

#[Route(
    '/api/healthcare-organizations/{organizationId}/care-team-assignments',
    requirements: ['organizationId' => '\\d+']
)]
#[OA\Tag(
    name: 'Healthcare - Care Team Assignments',
    description: 'Affectation des patients aux professionnels de santé d’une organisation'
)]
class CareTeamAssignmentController extends AbstractController
{
    public function __construct(
        private readonly CareTeamAssignmentService $service
    ) {}

    #[Route('', name: 'api_care_team_assignments_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Affecter un patient à un professionnel',
        description: 'Réservé à l’administrateur de l’organisation ciblée.'
    )]
    #[OA\Parameter(
        name: 'organizationId',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', format: 'int64', example: 2)
    )]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: CareTeamAssignmentRequestDTO::class)))]
    #[OA\Response(response: 201, description: 'Affectation créée', content: new OA\JsonContent(ref: new Model(type: CareTeamAssignmentResponseDTO::class)))]
    #[OA\Response(response: 403, description: 'Administrateur non autorisé pour cette organisation')]
    public function create(
        string $organizationId,
        #[MapRequestPayload] CareTeamAssignmentRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->create($organizationId, $dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('', name: 'api_care_team_assignments_list', methods: ['GET'])]
    #[OA\Get(summary: 'Lister les affectations d’une organisation')]
    public function list(string $organizationId): JsonResponse
    {
        $feedback = $this->service->list($organizationId);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{assignmentId}', name: 'api_care_team_assignments_get', requirements: ['assignmentId' => '\\d+'], methods: ['GET'])]
    #[OA\Get(summary: 'Consulter une affectation')]
    public function get(string $organizationId, string $assignmentId): JsonResponse
    {
        $feedback = $this->service->get($organizationId, $assignmentId);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{assignmentId}', name: 'api_care_team_assignments_update', requirements: ['assignmentId' => '\\d+'], methods: ['PUT'])]
    #[OA\Put(summary: 'Modifier une affectation')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: CareTeamAssignmentRequestDTO::class)))]
    public function update(
        string $organizationId,
        string $assignmentId,
        #[MapRequestPayload] CareTeamAssignmentRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->update($organizationId, $assignmentId, $dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{assignmentId}', name: 'api_care_team_assignments_delete', requirements: ['assignmentId' => '\\d+'], methods: ['DELETE'])]
    #[OA\Delete(summary: 'Supprimer une affectation')]
    #[OA\Response(response: 204, description: 'Affectation supprimée')]
    public function delete(string $organizationId, string $assignmentId): JsonResponse
    {
        $feedback = $this->service->delete($organizationId, $assignmentId);

        if (!$feedback->hasErrors()) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return $this->json($feedback, $feedback->getStatus());
    }
}
