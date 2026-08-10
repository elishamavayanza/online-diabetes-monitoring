<?php

namespace App\Controller\Api\Audit;

use App\DTO\Request\Audit\DataAccessLogRequestDTO;
use App\DTO\Response\Audit\DataAccessLogResponseDTO;
use App\Service\Audit\DataAccessLogService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/data-access-logs')]
#[OA\Tag(name: 'Audit - Data Access Logs', description: 'Journalisation et traçabilité de l’accès aux dossiers médicaux sensibles (conformité RGPD / HDS)')]
class DataAccessLogController extends AbstractController
{
    public function __construct(
        private readonly DataAccessLogService $service
    ) {}

    #[Route('', name: 'api_data_access_logs_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer un journal d’accès aux données',
        description: 'Permet de tracer de manière sécurisée chaque consultation ou modification d’une ressource liée à un patient par un professionnel de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du journal d’accès',
        content: new OA\JsonContent(
            required: ['accessedById', 'patientId', 'resourceType', 'resourceId', 'accessedAt'],
            properties: [
                new OA\Property(property: 'accessedById', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique de l’utilisateur ayant accédé à la ressource'),
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant unique du patient concerné par la donnée'),
                new OA\Property(property: 'resourceType', type: 'string', maxLength: 150, example: 'MedicalRecord', description: 'Type de la ressource consultée (ex: MedicalRecord, Prescription)'),
                new OA\Property(property: 'resourceId', type: 'string', format: 'uuid', example: 'd3b07384-d113-4ec6-a578-832f01f4c74a', description: 'Identifiant unique de la ressource ciblée'),
                new OA\Property(property: 'reason', type: 'string', maxLength: 5000, nullable: true, example: 'Consultation d’urgence dans le cadre d’une hospitalisation', description: 'Motif justifiant l’accès aux données'),
                new OA\Property(property: 'accessedAt', type: 'string', format: 'date-time', example: '2026-08-10T11:15:00Z', description: 'Date et heure exactes de l’accès')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Journal d’accès enregistré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Journal d’accès enregistré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DataAccessLogResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données de journalisation invalides',
        content: new OA\JsonContent(
            example: [
                'status' => 400,
                'error' => true,
                'message' => 'Patient introuvable.',
                'data' => null
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] DataAccessLogRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
