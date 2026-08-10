<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageReadReceiptRequestDTO;
use App\Service\Communication\MessageReadReceiptService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-read-receipts')]
#[OA\Tag(name: 'Communication - Message Read Receipts', description: 'Gestion des accusés de lecture des messages')]
class MessageReadReceiptController extends AbstractController
{
    public function __construct(
        private readonly MessageReadReceiptService $service
    ) {}

    #[Route('', name: 'api_message_read_receipts_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Marquer un message comme lu',
        description: 'Permet d’enregistrer l’accusé de lecture d’un message par un participant spécifique de la conversation.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’accusé de lecture',
        content: new OA\JsonContent(
            required: ['messageId', 'participantId', 'readAt'],
            properties: [
                new OA\Property(property: 'messageId', type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant unique du message lu'),
                new OA\Property(property: 'participantId', type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique du participant ayant lu le message'),
                new OA\Property(property: 'readAt', type: 'string', format: 'date-time', example: '2026-08-10T11:32:00Z', description: 'Date et heure exactes de la lecture')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Accusé de lecture enregistré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Accusé de lecture enregistré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Communication\MessageReadReceiptResponseDTO::class))
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
    public function create(#[MapRequestPayload] MessageReadReceiptRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
