<?php

namespace App\Controller\Api\Common;

use App\DTO\Feedback;
use App\DTO\Response\Common\FileAttachmentResponseDTO;
use App\Service\Common\FileAttachmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/file-attachments', name: 'api_file_attachments_')]
#[OA\Tag(name: 'Common - File Attachments', description: 'Gestion centralisée des pièces jointes et fichiers associés aux différentes entités')]
class FileAttachmentController extends AbstractController
{
    public function __construct(
        private readonly FileAttachmentService $fileAttachmentService
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’uploader physiquement un fichier (image, PDF, audio vocal, etc.) et d’enregistrer ses métadonnées.',
        summary: 'Uploader et enregistrer une nouvelle pièce jointe'
    )]
    #[OA\RequestBody(
        description: 'Fichier et paramètres de liaison (entityType, entityId)',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['file', 'entityType', 'entityId'],
                properties: [
                    new OA\Property(property: 'file', description: 'Le fichier binaire à uploader', type: 'string', format: 'binary'),
                    new OA\Property(property: 'entityType', description: 'Le type d entité liée (ex: Patient, Consultation, Message)', type: 'string', example: 'Patient'),
                    new OA\Property(property: 'entityId', description: 'L identifiant de l entité liée', type: 'string', example: '9a882211-12ee-4c55-8811-1a2233445566')
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Pièce jointe enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Pièce jointe enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: FileAttachmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Fichier manquant ou paramètres invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        $entityType = $request->request->get('entityType');
        $entityId = $request->request->get('entityId');

        if (!$file || !$entityType || !$entityId) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Les champs file, entityType et entityId sont obligatoires.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->fileAttachmentService->uploadAndCreate($file, $entityType, $entityId);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet d’obtenir les métadonnées complètes d’une pièce jointe.',
        summary: 'Récupérer les détails d’une pièce jointe'
    )]
    #[OA\Response(
        response: 200,
        description: 'Détails récupérés avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Succès'),
                new OA\Property(property: 'data', ref: new Model(type: FileAttachmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Pièce jointe introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function show(string $id): JsonResponse
    {
        $feedback = $this->fileAttachmentService->getById($id);

        if ($feedback->hasErrors()) {
            return $this->json($feedback, Response::HTTP_NOT_FOUND);
        }

        return $this->json($feedback, Response::HTTP_OK);
    }
}
