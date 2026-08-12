<?php

namespace App\Controller\Api\Common;

use App\DTO\Request\Common\FileAttachmentRequestDTO;
use App\DTO\Response\Common\FileAttachmentResponseDTO;
use App\Service\Common\FileAttachmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/file-attachments', name: 'api_file_attachments_')]
#[OA\Tag(name: 'Common - File Attachments', description: 'Gestion centralisée des pièces jointes et fichiers associés aux différentes entités de la plateforme')]
class FileAttachmentController extends AbstractController
{
    public function __construct(
        private readonly FileAttachmentService $fileAttachmentService
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une nouvelle pièce jointe',
        description: 'Permet d’associer les métadonnées d’un fichier stocké à une entité spécifique du système (ex: Patient, Consultation, Ordonnance).'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Métadonnées du fichier à enregistrer',
        content: new OA\JsonContent(
            ref: new Model(type: FileAttachmentRequestDTO::class)
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
    #[OA\Response(
        response: 400,
        description: 'Erreur de validation ou métadonnées invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] FileAttachmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->fileAttachmentService->create($dto);

        return $this->json($feedback, $feedback->getStatus());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupérer les détails d’une pièce jointe',
        description: 'Permet d’obtenir les métadonnées complètes d’une pièce jointe à partir de son identifiant unique.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant UUID de la pièce jointe',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid', example: '9a882211-12ee-4c55-8811-1a2233445566')
    )]
    #[OA\Response(
        response: 200,
        description: 'Détails de la pièce jointe récupérés avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Succès'),
                new OA\Property(property: 'data', ref: new Model(type: FileAttachmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Pièce jointe introuvable'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function show(string $id): JsonResponse
    {
        $feedback = $this->fileAttachmentService->getById($id);

        if ($feedback->hasErrors()) {
            return $this->json($feedback, Response::HTTP_NOT_FOUND);
        }

        return $this->json($feedback, Response::HTTP_OK);
    }
}
