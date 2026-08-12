<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\PhysicalActivityMeasurementRequestDTO;
use App\DTO\Response\Medical\PhysicalActivityMeasurementResponseDTO;
use App\Service\Medical\PhysicalActivityMeasurementService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/physical-activity-measurements')]
#[OA\Tag(name: 'Medical - Physical Activity', description: 'Gestion des mesures d’activité physique')]
class PhysicalActivityMeasurementController extends AbstractController
{
    public function __construct(
        private readonly PhysicalActivityMeasurementService $service
    ) {}

    #[Route('', name: 'api_physical_activity_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une activité physique',
        description: 'Permet d’ajouter une séance d’activité physique pour un patient (durée, calories, fréquence cardiaque).'
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
        description: 'Paramètres de l’activité physique',
        content: new OA\JsonContent(
            ref: new Model(type: PhysicalActivityMeasurementRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Activité physique enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Activité physique enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PhysicalActivityMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] PhysicalActivityMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
