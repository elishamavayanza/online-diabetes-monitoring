<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\HbA1cMeasurementRequestDTO;
use App\DTO\Response\Medical\HbA1cMeasurementResponseDTO;
use App\Service\Medical\HbA1cMeasurementService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/hba1c-measurements')]
#[OA\Tag(name: 'Medical - HbA1c', description: 'Gestion des mesures d’HbA1c (hémoglobine glyquée)')]
class HbA1cMeasurementController extends AbstractController
{
    public function __construct(
        private readonly HbA1cMeasurementService $service
    ) {}

    #[Route('', name: 'api_hba1c_get_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de toutes les mesures d’HbA1c pour un patient spécifique.',
        summary: 'Voir toutes les mesures d’HbA1c d’un patient'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des mesures récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesures d’HbA1c récupérées avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: HbA1cMeasurementResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function getByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_hba1c_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’une mesure d’HbA1c spécifique par son ID.',
        summary: 'Voir une mesure d’HbA1c par ID'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique de la mesure d’HbA1c',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Mesure récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure d’HbA1c récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HbA1cMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Mesure ou patient introuvable')]
    public function getById(string $patientId, int $id): JsonResponse
    {
        $feedback = $this->service->getById($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_hba1c_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ajouter une nouvelle mesure d’hémoglobine glyquée (HbA1c) pour un patient spécifique.',
        summary: 'Enregistrer une mesure d’HbA1c'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la mesure d’HbA1c',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HbA1cMeasurementRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Mesure d’HbA1c enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure d’HbA1c enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HbA1cMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] HbA1cMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_hba1c_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Met à jour une mesure d’HbA1c existante.',
        summary: 'Mettre à jour une mesure d’HbA1c'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique de la mesure d’HbA1c',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Paramètres mis à jour de la mesure',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HbA1cMeasurementRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Mesure mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure d’HbA1c mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HbA1cMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Mesure introuvable')]
    public function update(string $patientId, int $id, #[MapRequestPayload] HbA1cMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($patientId, $id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_hba1c_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime une mesure d’HbA1c existante.',
        summary: 'Supprimer une mesure d’HbA1c'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique de la mesure d’HbA1c',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Mesure supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Mesure d’HbA1c supprimée avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Mesure introuvable')]
    public function delete(string $patientId, int $id): JsonResponse
    {
        $feedback = $this->service->delete($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
