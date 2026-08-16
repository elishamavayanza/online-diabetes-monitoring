<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\WeightMeasurementRequestDTO;
use App\DTO\Response\Medical\WeightMeasurementResponseDTO;
use App\Service\Medical\WeightMeasurementService;
use Nelmio\ApiDocBundle\Attribute\Model;
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

    #[Route('', name: 'api_weight_measurements_all', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de toutes les mesures de poids d’un patient.',
        summary: 'Lister les mesures de poids'
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
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: WeightMeasurementResponseDTO::class))
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function all(string $patientId): JsonResponse
    {
        $feedback = $this->service->all($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_weight_measurements_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’une mesure de poids spécifique.',
        summary: 'Afficher une mesure de poids'
    )]
    #[OA\Parameter(name: 'patientId', description: 'Identifiant unique du patient', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Mesure récupérée avec succès',
        content: new OA\JsonContent(ref: new Model(type: WeightMeasurementResponseDTO::class))
    )]
    #[OA\Response(response: 404, description: 'Mesure ou patient non trouvé')]
    public function get(string $patientId, string $id): JsonResponse
    {
        $feedback = $this->service->get($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_weight_measurements_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer le poids et optionnellement la taille d’un patient pour le calcul de l’IMC.',
        summary: 'Enregistrer une mesure de poids'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la mesure pondérale',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: WeightMeasurementRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: WeightMeasurementResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] WeightMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_weight_measurements_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une mesure de poids existante.',
        summary: 'Mettre à jour une mesure de poids'
    )]
    #[OA\Parameter(name: 'patientId', description: 'Identifiant unique du patient', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: WeightMeasurementRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Mesure mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Mesure non trouvée')]
    public function update(string $patientId, string $id, #[MapRequestPayload] WeightMeasurementRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($patientId, $id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_weight_measurements_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une mesure de poids.',
        summary: 'Supprimer une mesure de poids'
    )]
    #[OA\Parameter(name: 'patientId', description: 'Identifiant unique du patient', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'id', description: 'Identifiant unique de la mesure', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Mesure supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Mesure non trouvée')]
    public function delete(string $patientId, string $id): JsonResponse
    {
        $feedback = $this->service->delete($patientId, $id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
