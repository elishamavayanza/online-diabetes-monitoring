<?php

namespace App\Controller\Api\Communication;

use App\DTO\Feedback;
use App\DTO\Response\Communication\MessageAttachmentResponseDTO;
use App\Service\Communication\MessageAttachmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-attachments')]
#[OA\Tag(name: 'Communication - Message Attachments', description: 'Gestion des pièces jointes et messages vocaux associés aux messages')]
class MessageAttachmentController extends AbstractController
{
    public function __construct(
        private readonly MessageAttachmentService $service
    ) {}

    #[Route('', name: 'api_message_attachments_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’uploader et de joindre un fichier (image, PDF, Word ou message vocal audio) à un message existant.',
        summary: 'Uploader une pièce jointe ou un vocal'
    )]
    #[OA\RequestBody(
        description: 'Fichier ou audio à uploader (multipart/form-data)',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['messageId', 'file'],
                properties: [
                    new OA\Property(property: 'messageId', type: 'string', example: '1'),
                    new OA\Property(property: 'file', description: 'Le fichier ou audio à joindre (PNG, JPG, PDF, DOCX, WEBM...)', type: 'string', format: 'binary')
                ]
            )
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
                new OA\Property(property: 'data', ref: new Model(type: MessageAttachmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête ou fichier invalide')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(Request $request): JsonResponse
    {
        $messageId = $request->request->get('messageId');
        $file = $request->files->get('file');

        if (!$messageId || !$file) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Les paramètres messageId et file sont obligatoires.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->service->upload($messageId, $file);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}/download', name: 'api_message_attachments_download', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de télécharger le fichier joint ou l’audio associé à un identifiant de pièce jointe.',
        summary: 'Télécharger une pièce jointe ou un vocal'
    )]
    #[OA\Response(
        response: 200,
        description: 'Fichier téléchargé avec succès'
    )]
    #[OA\Response(response: 404, description: 'Pièce jointe ou fichier introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function download(string $id): BinaryFileResponse|JsonResponse
    {
        $result = $this->service->download($id);

        if ($result instanceof Feedback) {
            return $this->json($result, Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}
