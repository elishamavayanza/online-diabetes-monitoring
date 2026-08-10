<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionRequestDTO;
use App\Service\Treatment\PrescriptionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescriptions')]
#[OA\Tag(name: 'Treatment - Prescriptions', description: 'Gestion des ordonnances et prescriptions médicales')]
class PrescriptionController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionService $service
    ) {}

    #[Route('', name: 'api_prescriptions_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une prescription',
        description: 'Permet d’émettre une nouvelle prescription médicale pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la prescription',
        content: new OA\JsonContent(
            required: ['patientId', 'prescriberId', 'organizationId', 'startDate', 'status'],
            properties: [
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient'),
                new OA\Property(property: 'prescriberId', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du prescripteur (médecin)'),
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation/hôpital'),
                new OA\Property(property: 'startDate', type: 'string', format: 'date-time', example: '2026-08-10T00:00:00Z', description: 'Date de début du traitement'),
                new OA\Property(property: 'endDate', type: 'string', format: 'date-time', nullable: true, example: '2026-08-17T00:00:00Z', description: 'Date de fin du traitement'),
                new OA\Property(property: 'status', type: 'string', example: 'ACTIVE', description: 'Statut de la prescription'),
                new OA\Property(property: 'notes', type: 'string', maxLength: 5000, nullable: true, example: 'À prendre pendant les repas.', description: 'Notes cliniques'),
                new OA\Property(property: 'validatedAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de validation'),
                new OA\Property(property: 'validatedById', type: 'string', format: 'uuid', nullable: true, example: null, description: 'ID de l’utilisateur ayant validé')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Prescription créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prescription créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Treatment\PrescriptionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
