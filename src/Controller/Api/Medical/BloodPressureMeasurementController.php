<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\BloodPressureMeasurementRequestDTO;
use App\Service\Medical\BloodPressureMeasurementService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/blood-pressure-measurements')]
#[OA\Tag(name: 'Medical - Blood Pressure', description: 'Gestion des mesures de tension artérielle')]
class BloodPressureMeasurementController extends AbstractController
{
    public function __construct(
        private readonly BloodPressureMeasurementService $service
    ) {}

    #[Route('', name: 'api_blood_pressure_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une mesure de tension artérielle',
        description: 'Permet d’ajouter une nouvelle mesure de pression artérielle (systolique, diastolique et pouls) pour un patient.'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la pression artérielle',
        content: new OA\JsonContent(
            required: ['systolic', 'diastolic'],
            properties: [
                new OA\Property(property: 'systolic', type: 'string', example: '120', description: 'Pression systolique (mmHg)'),
                new OA\Property(property: 'diastolic', type: 'string', example: '80', description: 'Pression diastolique (mmHg)'),
                new OA\Property(property: 'pulse', type: 'string', nullable: true, example: '72', description: 'Fréquence cardiaque / pouls (bpm)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Mesure de tension artérielle enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Medical\BloodPressureMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] BloodPressureMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
