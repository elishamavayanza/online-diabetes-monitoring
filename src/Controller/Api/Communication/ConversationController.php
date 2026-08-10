<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\ConversationRequestDTO;
use App\Service\Communication\ConversationService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversations')]
#[OA\Tag(name: 'Communication - Conversations', description: 'Gestion des fils de discussion et des salons de messagerie de la plateforme')]
class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ConversationService $service
    ) {}

    #[Route('', name: 'api_conversations_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une nouvelle conversation',
        description: 'Permet d’initialiser un nouveau fil de discussion (sujet) rattaché ou non à une organisation de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de création de la conversation',
        content: new OA\JsonContent(
            required: ['subject', 'createdById'],
            properties: [
                new OA\Property(property: 'subject', type: 'string', maxLength: 255, example: 'Suivi post-opératoire du dossier patient #42', description: 'Sujet ou objet de la conversation'),
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', nullable: true, example: '1c552144-88ef-4a92-b4c4-7893a12b4e55', description: 'Identifiant de l’organisation associée (optionnel)'),
                new OA\Property(property: 'createdById', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant de l’utilisateur à l’origine de la création'),
                new OA\Property(property: 'closedAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture éventuelle de la conversation')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Conversation créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Conversation créée avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ConversationResponseDTO')
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
    public function create(#[MapRequestPayload] ConversationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
