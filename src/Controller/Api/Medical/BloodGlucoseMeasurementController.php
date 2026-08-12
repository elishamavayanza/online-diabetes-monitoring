<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\BloodGlucoseMeasurementRequestDTO;
use App\DTO\Response\Medical\BloodGlucoseMeasurementResponseDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Service\Medical\BloodGlucoseMeasurementService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/blood-glucose-measurements')]
#[OA\Tag(name: 'Medical - Blood Glucose', description: 'Gestion des mesures de glycémie')]
class BloodGlucoseMeasurementController extends AbstractController
{
    public function __construct(
        private readonly BloodGlucoseMeasurementService $service
    ) {}

    #[Route('', name: 'api_blood_glucose_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une mesure de glycémie',
        description: 'Permet d’ajouter une nouvelle mesure de glycémie pour un patient spécifique.'
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
        description: 'Paramètres de la mesure de glycémie',
        content: new OA\JsonContent(
            ref: new Model(type: BloodGlucoseMeasurementRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Mesure de glycémie enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure de glycémie enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: BloodGlucoseMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] BloodGlucoseMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
