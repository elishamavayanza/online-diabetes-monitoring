<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\WeightMeasurementRequestDTO;
use App\Service\Medical\WeightMeasurementService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/weight-measurements')]
#[OA\Tag(name: 'Medical - Weight & BMI', description: 'Gestion des mesures de poids et de l’IMC')]
class WeightMeasurementController extends AbstractController
{
    public function __construct(
        private readonly WeightMeasurementService $service
    ) {}

    #[Route('', name: 'api_weight_measurements_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une mesure de poids',
        description: 'Permet d’enregistrer le poids et optionnellement la taille d’un patient pour le calcul de l’IMC.'
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
        description: 'Paramètres de la mesure pondérale',
        content: new OA\JsonContent(
            required: ['valueKg'],
            properties: [
                new OA\Property(property: 'valueKg', type: 'string', example: '75.50', description: 'Poids en kilogrammes (kg)'),
                new OA\Property(property: 'heightCm', type: 'string', nullable: true, example: '175.00', description: 'Taille en centimètres (cm)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Mesure de poids enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure de poids enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/WeightMeasurementResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] WeightMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
