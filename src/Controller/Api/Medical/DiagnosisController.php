<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\Service\Medical\DiagnosisService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnoses')]
#[OA\Tag(name: 'Medical - Diagnoses', description: 'Gestion des diagnostics médicaux')]
class DiagnosisController extends AbstractController
{
    public function __construct(
        private readonly DiagnosisService $service
    ) {}

    #[Route('', name: 'api_diagnoses_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un diagnostic médical',
        description: 'Permet à un professionnel de santé d’établir et d’enregistrer un diagnostic pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du diagnostic',
        content: new OA\JsonContent(
            required: ['patientId', 'doctorId', 'conditionName', 'diagnosedAt', 'status'],
            properties: [
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient'),
                new OA\Property(property: 'doctorId', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'ID du médecin traitant'),
                new OA\Property(property: 'conditionName', type: 'string', maxLength: 150, example: 'Diabète de type 2', description: 'Nom de la pathologie / affection'),
                new OA\Property(property: 'description', type: 'string', maxLength: 5000, nullable: true, example: 'Patient présentant une hyperglycémie chronique.', description: 'Description détaillée'),
                new OA\Property(property: 'diagnosedAt', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date et heure du diagnostic'),
                new OA\Property(property: 'status', type: 'string', maxLength: 50, example: 'CONFIRMED', description: 'Statut du diagnostic (CONFIRMED, SUSPECTED, RESOLVED)'),
                new OA\Property(property: 'medicalRecordId', type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du dossier médical associé (optionnel)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Diagnostic créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Medical\DiagnosisResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] DiagnosisRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
