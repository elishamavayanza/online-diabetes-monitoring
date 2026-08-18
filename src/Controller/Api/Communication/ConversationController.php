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
        description: 'Permet d’initialiser un nouveau fil de discussion (sujet) rattaché ou non à une organisation de santé.',
        summary: 'Créer une nouvelle conversation'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de création de la conversation',
        required: true,
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
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] ConversationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_conversations_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’une conversation spécifique par son identifiant.',
        summary: 'Récupérer une conversation'
    )]
    #[OA\Response(
        response: 200,
        description: 'Conversation récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Conversation récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: ConversationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Conversation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function get(string $id): JsonResponse
    {
        $feedback = $this->service->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_conversations_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de mettre à jour une conversation existante.',
        summary: 'Mettre à jour une conversation'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de mise à jour de la conversation',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: ConversationRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Conversation mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Conversation mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: ConversationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Conversation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(string $id, #[MapRequestPayload] ConversationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_conversations_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une conversation par son identifiant.',
        summary: 'Supprimer une conversation'
    )]
    #[OA\Response(
        response: 200,
        description: 'Conversation supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Conversation supprimée avec succès.'),
                new OA\Property(property: 'data', type: 'null', example: null)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Conversation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
