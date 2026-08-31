<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionRequestDTO;
use App\DTO\Response\Treatment\PrescriptionResponseDTO;
use App\Service\Treatment\PrescriptionService;
use Nelmio\ApiDocBundle\Attribute\Model;
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

    #[Route('/patient/{patientId}', name: 'api_prescriptions_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer toutes les prescriptions d’un patient.',
        summary: 'Lister les prescriptions d’un patient'
    )]
    #[OA\Response(response: 200, description: 'Prescriptions récupérées avec succès')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function listByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescriptions_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’une prescription par son ID.',
        summary: 'Afficher une prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Prescription récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prescription récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Prescription introuvable')]
    public function show(string $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_prescriptions_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’émettre une nouvelle prescription médicale pour un patient.',
        summary: 'Créer une prescription'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la prescription',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescriptions_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de mettre à jour une prescription médicale existante.',
        summary: 'Mettre à jour une prescription'
    )]
    #[OA\RequestBody(
        description: 'Paramètres modifiés de la prescription',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Prescription mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prescription mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Prescription introuvable')]
    public function update(string $id, #[MapRequestPayload] PrescriptionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescriptions_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une prescription médicale.',
        summary: 'Supprimer une prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Prescription supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prescription supprimée avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Prescription introuvable')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
