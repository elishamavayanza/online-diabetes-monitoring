<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\ExternalFollowInvitationRequestDTO;
use App\DTO\Request\Healthcare\ExternalFollowRenewRequestDTO;
use App\DTO\Response\Healthcare\ExternalFollowInvitationResponseDTO;
use App\DTO\Response\Healthcare\ExternalFollowLogResponseDTO;
use App\Service\Healthcare\ExternalFollowService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/api/healthcare-organizations/{organizationId}/external-follows',
    requirements: ['organizationId' => '\\d+']
)]
#[OA\Tag(
    name: 'Healthcare - Suivi externe',
    description: 'Invitation de professionnels d’autres organisations à suivre un patient pour une durée définie'
)]
class ExternalFollowAdminController extends AbstractController
{
    public function __construct(
        private readonly ExternalFollowService $service
    ) {}

    #[Route('', name: 'api_external_follows_list', methods: ['GET'])]
    #[OA\Get(summary: 'Lister les invitations de suivi externe')]
    #[OA\Response(response: 200, description: 'Liste des invitations', content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))
        ]
    ))]
    public function list(string $organizationId): JsonResponse
    {
        $feedback = $this->service->list($organizationId);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('', name: 'api_external_follows_create', methods: ['POST'])]
    #[OA\Post(summary: 'Inviter un professionnel d’une autre organisation à suivre un patient')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationRequestDTO::class)))]
    #[OA\Response(response: 201, description: 'Invitation créée', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function create(
        string $organizationId,
        #[MapRequestPayload] ExternalFollowInvitationRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->create($organizationId, $dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{invitationId}/renew', name: 'api_external_follows_renew', requirements: ['invitationId' => '\\d+'], methods: ['POST'])]
    #[OA\Post(summary: 'Prolonger le délai d’une invitation')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: ExternalFollowRenewRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Délai prolongé', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function renew(
        string $organizationId,
        string $invitationId,
        #[MapRequestPayload] ExternalFollowRenewRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->renew($organizationId, $invitationId, $dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{invitationId}/revoke', name: 'api_external_follows_revoke', requirements: ['invitationId' => '\\d+'], methods: ['POST'])]
    #[OA\Post(summary: 'Couper immédiatement l’accès d’un professionnel externe')]
    #[OA\Response(response: 200, description: 'Accès coupé', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function revoke(string $organizationId, string $invitationId): JsonResponse
    {
        $feedback = $this->service->revoke($organizationId, $invitationId);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/logs', name: 'api_external_follows_logs', methods: ['GET'])]
    #[OA\Get(summary: 'Journal des actions des professionnels externes dans les dossiers patients')]
    #[OA\Parameter(name: 'invitationId', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Journal récupéré', content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: ExternalFollowLogResponseDTO::class)))
        ]
    ))]
    public function logs(string $organizationId, Request $request): JsonResponse
    {
        $invitationId = $request->query->get('invitationId');
        $feedback = $this->service->logs($organizationId, $invitationId !== null ? (string) $invitationId : null);

        return $this->json($feedback, $feedback->getStatus());
    }
}