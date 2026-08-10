<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\ConversationParticipantRequestDTO;
use App\Service\Communication\ConversationParticipantService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversation-participants')]
#[OA\Tag(name: 'Communication - Conversation Participants', description: 'Gestion des participants rattachés aux fils de discussion de la plateforme')]
class ConversationParticipantController extends AbstractController
{
    public function __construct(
        private readonly ConversationParticipantService $service
    ) {}

    #[Route('', name: 'api_conversation_participants_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajouter un participant à une conversation',
        description: 'Permet d’assigner un utilisateur en tant que participant à un fil de discussion existant.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres d’adhésion du participant',
        content: new OA\JsonContent(
            required: ['conversationId', 'userId', 'joinedAt'],
            properties: [
                new OA\Property(property: 'conversationId', type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique de la conversation cible'),
                new OA\Property(property: 'userId', type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant unique de l’utilisateur à ajouter'),
                new OA\Property(property: 'joinedAt', type: 'string', format: 'date-time', example: '2026-08-10T09:15:00Z', description: 'Date et heure d’adhésion à la conversation'),
                new OA\Property(property: 'leftAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date et heure de départ de la conversation (le cas échéant)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Participant ajouté avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Participant ajouté avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ConversationParticipantResponseDTO')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides ou utilisateur déjà participant'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] ConversationParticipantRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
