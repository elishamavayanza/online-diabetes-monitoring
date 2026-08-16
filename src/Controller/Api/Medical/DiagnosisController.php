<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\DTO\Response\Medical\DiagnosisResponseDTO;
use App\Service\Medical\DiagnosisService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnoses')]
#[OA\Tag(name: 'Medical - Diagnoses', description: 'Gestion des diagnostics médicaux')]
class DiagnosisController extends AbstractController
{
    public function __construct(
        private readonly DiagnosisService $service
    ) {}

    #[Route('/{id}', name: 'api_diagnoses_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’un diagnostic spécifique par son ID.',
        summary: 'Voir un diagnostic par ID'
    )]
    #[OA\Response(
        response: 200,
        description: 'Diagnostic récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DiagnosisResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Diagnostic introuvable')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/patient/{patientId}', name: 'api_diagnoses_get_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les diagnostics associés à un patient spécifique.',
        summary: 'Voir tous les diagnostics d’un patient'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des diagnostics récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostics récupérés avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: DiagnosisResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getByPatient(int $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_diagnoses_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet à un utilisateur (patient ou professionnel) d’établir et d’enregistrer un diagnostic.',
        summary: 'Créer un diagnostic médical'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du diagnostic',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: DiagnosisRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Diagnostic créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DiagnosisResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] DiagnosisRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }



    #[Route('/{id}', name: 'api_diagnoses_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Met à jour un diagnostic existant.',
        summary: 'Mettre à jour un diagnostic'
    )]
    #[OA\RequestBody(
        description: 'Paramètres modifiés du diagnostic',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: DiagnosisRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Diagnostic mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DiagnosisResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Diagnostic introuvable')]
    public function update(int $id, #[MapRequestPayload] DiagnosisRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_diagnoses_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime un diagnostic existant.',
        summary: 'Supprimer un diagnostic'
    )]
    #[OA\Response(
        response: 200,
        description: 'Diagnostic supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic supprimé avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Diagnostic introuvable')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
