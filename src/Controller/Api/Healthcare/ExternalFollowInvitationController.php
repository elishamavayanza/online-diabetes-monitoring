<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\ExternalFollowCloseRequestDTO;
use App\DTO\Response\Healthcare\ExternalFollowInvitationResponseDTO;
use App\Service\Healthcare\ExternalFollowService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/external-follows')]
#[OA\Tag(
    name: 'Healthcare - Suivi externe',
    description: 'Réponses du professionnel invité aux invitations de suivi'
)]
class ExternalFollowInvitationController extends AbstractController
{
    public function __construct(
        private readonly ExternalFollowService $service
    ) {}

    #[Route('/invitations/{token}', name: 'api_external_follows_get_by_token', methods: ['GET'])]
    #[OA\Get(summary: 'Consulter une invitation par son jeton (page publique d’acceptation)')]
    #[OA\Response(response: 200, description: 'Invitation', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function getByToken(string $token): JsonResponse
    {
        $feedback = $this->service->getByToken($token);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/invitations/{token}/accept', name: 'api_external_follows_accept', methods: ['POST'])]
    #[OA\Post(summary: 'Accepter une invitation (artisan professionnel dont l’email correspond)')]
    #[OA\Response(response: 200, description: 'Invitation acceptée', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function accept(string $token): JsonResponse
    {
        $feedback = $this->service->accept($token);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/invitations/{token}/decline', name: 'api_external_follows_decline', methods: ['POST'])]
    #[OA\Post(summary: 'Refuser une invitation')]
    #[OA\Response(response: 200, description: 'Invitation refusée', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    public function decline(string $token): JsonResponse
    {
        $feedback = $this->service->decline($token);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/my', name: 'api_external_follows_my', methods: ['GET'])]
    #[OA\Get(summary: 'Suivis externes actifs du professionnel connecté')]
    #[OA\Response(response: 200, description: 'Liste des suivis', content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))
        ]
    ))]
    public function my(): JsonResponse
    {
        $feedback = $this->service->myFollows();

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/my/{invitationId}/close', name: 'api_external_follows_close_my', requirements: ['invitationId' => '\\d+'], methods: ['POST'])]
    #[OA\Post(summary: 'Fermer son suivi externe (uniquement pour soi, motif notifié aux admins de l’organisation d’origine)')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: ExternalFollowCloseRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Suivi fermé', content: new OA\JsonContent(ref: new Model(type: ExternalFollowInvitationResponseDTO::class)))]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    #[OA\Response(response: 422, description: 'Motif manquant ou suivi dans un état incompatible')]
    public function closeMy(string $invitationId, #[MapRequestPayload] ExternalFollowCloseRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->closeMyFollow($invitationId, $dto);

        return $this->json($feedback, $feedback->getStatus());
    }
}