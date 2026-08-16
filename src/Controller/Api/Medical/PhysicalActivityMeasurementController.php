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

    #[Route('', name: 'api_physical_activity_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste des mesures d’activité physique d’un patient.',
        summary: 'Lister les activités physiques d’un patient'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function list(string $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{measurementId}', name: 'api_physical_activity_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer une mesure d’activité physique spécifique par son identifiant.',
        summary: 'Récupérer une activité physique par ID'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'measurementId', description: 'Identifiant de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Mesure récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Patient ou mesure non trouvée')]
    public function getById(string $patientId, string $measurementId): JsonResponse
    {
        $feedback = $this->service->getById($patientId, $measurementId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }


    #[Route('', name: 'api_physical_activity_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ajouter une séance d’activité physique pour un patient (durée, calories, fréquence cardiaque).',
        summary: 'Enregistrer une activité physique'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’activité physique',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PhysicalActivityMeasurementRequestDTO::class)
        )
    )]
    #[OA\Response(response: 201, description: 'Activité physique enregistrée avec succès')]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] PhysicalActivityMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }


    #[Route('/{measurementId}', name: 'api_physical_activity_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une mesure d’activité physique existante.',
        summary: 'Modifier une activité physique'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'measurementId', description: 'Identifiant de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: PhysicalActivityMeasurementRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Mesure mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Patient ou mesure non trouvée')]
    public function update(
        string $patientId,
        string $measurementId,
        #[MapRequestPayload] PhysicalActivityMeasurementRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->update($patientId, $measurementId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{measurementId}', name: 'api_physical_activity_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une mesure d’activité physique.',
        summary: 'Supprimer une activité physique'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'measurementId', description: 'Identifiant de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Mesure supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Patient ou mesure non trouvée')]
    public function delete(string $patientId, string $measurementId): JsonResponse
    {
        $feedback = $this->service->delete($patientId, $measurementId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
