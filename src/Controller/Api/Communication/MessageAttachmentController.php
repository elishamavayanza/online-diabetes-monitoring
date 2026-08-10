<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageAttachmentRequestDTO;
use App\Service\Communication\MessageAttachmentService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-attachments')]
#[OA\Tag(name: 'Communication - Message Attachments', description: 'Gestion des pièces jointes associées aux messages')]
class MessageAttachmentController extends AbstractController
{
    public function __construct(
        private readonly MessageAttachmentService $service
    ) {}

    #[Route('', name: 'api_message_attachments_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Associer une pièce jointe à un message',
        description: 'Permet de joindre un fichier (document, image) à un message existant dans une conversation.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Métadonnées du fichier joint au message',
        content: new OA\JsonContent(
            required: ['messageId', 'fileUrl', 'fileName', 'mimeType', 'sizeBytes'],
            properties: [
                new OA\Property(property: 'messageId', type: 'string', format: 'uuid', example: '9f881245-33ee-4b11-9a21-4f88e1478c99', description: 'Identifiant unique du message associé'),
                new OA\Property(property: 'fileUrl', type: 'string', format: 'uri', maxLength: 500, example: 'https://storage.diabcare.com/messages/bilan_sanguin.pdf', description: 'URL d’accès distant au fichier'),
                new OA\Property(property: 'fileName', type: 'string', maxLength: 255, example: 'bilan_sanguin.pdf', description: 'Nom du fichier'),
                new OA\Property(property: 'mimeType', type: 'string', maxLength: 150, example: 'application/pdf', description: 'Type MIME du fichier'),
                new OA\Property(property: 'sizeBytes', type: 'integer', example: 512000, description: 'Taille du fichier en octets')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Pièce jointe ajoutée au message avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Pièce jointe ajoutée avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/MessageAttachmentResponseDTO')
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
    public function create(#[MapRequestPayload] MessageAttachmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
