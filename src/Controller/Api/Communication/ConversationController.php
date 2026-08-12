<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\ConversationRequestDTO;
use App\DTO\Response\Communication\ConversationResponseDTO;
use App\Service\Communication\ConversationService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ref: new Model(type: ConversationRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: ConversationResponseDTO::class))
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
