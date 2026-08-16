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

    #[Route('', name: 'api_blood_glucose_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de lister toutes les mesures de glycémie d’un patient spécifique.',
        summary: 'Lister les mesures de glycémie'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Liste des mesures récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function index(string $patientId): JsonResponse
    {
        $feedback = $this->service->index($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_blood_glucose_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer une mesure de glycémie spécifique par son identifiant.',
        summary: 'Afficher une mesure de glycémie'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique (UUID) de la mesure',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Mesure récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Mesure ou patient non trouvé')]
    public function show(string $patientId, string $id): JsonResponse
    {
        $feedback = $this->service->show($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_blood_glucose_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ajouter une nouvelle mesure de glycémie pour un patient spécifique.',
        summary: 'Enregistrer une mesure de glycémie'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la mesure de glycémie',
        required: true,
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
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_blood_glucose_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une mesure de glycémie existante.',
        summary: 'Mettre à jour une mesure de glycémie'
    )]
    #[OA\Parameter(name: 'patientId', description: 'ID du patient', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'id', description: 'ID de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: BloodGlucoseMeasurementRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Mesure mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Mesure non trouvée')]
    public function update(string $patientId, string $id, #[MapRequestPayload] BloodGlucoseMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($patientId, $id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_blood_glucose_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une mesure de glycémie.',
        summary: 'Supprimer une mesure de glycémie'
    )]
    #[OA\Parameter(name: 'patientId', description: 'ID du patient', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'id', description: 'ID de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Mesure supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Mesure non trouvée')]
    public function delete(string $patientId, string $id): JsonResponse
    {
        $feedback = $this->service->delete($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
