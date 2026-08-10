<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageRequestDTO;
use App\Service\Communication\MessageService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/messages')]
#[OA\Tag(name: 'Communication - Messages', description: 'Gestion des messages échangés au sein des fils de discussion')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageService $service
    ) {}

    #[Route('', name: 'api_messages_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Envoyer un nouveau message',
        description: 'Permet d’émettre et d’enregistrer un message textuel à l’intérieur d’une conversation existante.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du message à envoyer',
        content: new OA\JsonContent(
            required: ['conversationId', 'senderId', 'content', 'sentAt'],
            properties: [
                new OA\Property(property: 'conversationId', type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique de la conversation cible'),
                new OA\Property(property: 'senderId', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique de l’utilisateur expéditeur'),
                new OA\Property(property: 'content', type: 'string', example: 'Bonjour, les analyses de biologie du patient sont disponibles.', description: 'Contenu textuel du message'),
                new OA\Property(property: 'sentAt', type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date et heure d’envoi effectif du message'),
                new OA\Property(property: 'editedAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date et heure de la dernière modification du message')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Message envoyé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Message envoyé avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/MessageResponseDTO')
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
    public function create(#[MapRequestPayload] MessageRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
