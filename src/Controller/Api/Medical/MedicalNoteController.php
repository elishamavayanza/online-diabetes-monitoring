<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\MedicalNoteRequestDTO;
use App\Service\Medical\MedicalNoteService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-notes')]
#[OA\Tag(name: 'Medical - Notes', description: 'Gestion des notes médicales')]
class MedicalNoteController extends AbstractController
{
    public function __construct(
        private readonly MedicalNoteService $service
    ) {}

    #[Route('', name: 'api_medical_notes_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une note médicale',
        description: 'Permet à un professionnel de santé d’ajouter une note clinique au dossier médical d’un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la note médicale',
        content: new OA\JsonContent(
            required: ['medicalRecordId', 'authorId', 'content', 'notedAt'],
            properties: [
                new OA\Property(property: 'medicalRecordId', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du dossier médical'),
                new OA\Property(property: 'authorId', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'ID de l’auteur (professionnel de santé)'),
                new OA\Property(property: 'content', type: 'string', example: 'Patient stable, bonne observance du traitement.', description: 'Contenu textuel de la note'),
                new OA\Property(property: 'notedAt', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date et heure de la prise de note')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Note médicale créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Note médicale créée avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/MedicalNoteResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicalNoteRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
