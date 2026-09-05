<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\MedicalRecordRequestDTO;
use App\DTO\Response\Medical\MedicalRecordResponseDTO;
use App\Service\Medical\MedicalRecordService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-records')]
#[OA\Tag(name: 'Medical - Records', description: 'Gestion des dossiers médicaux')]
class MedicalRecordController extends AbstractController
{
    public function __construct(
        private readonly MedicalRecordService $service
    ) {}

    #[Route('', name: 'api_medical_records_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’ouvrir un nouveau dossier médical pour un patient dans une organisation de santé.',
        summary: 'Créer un dossier médical'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du dossier médical',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicalRecordRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Dossier médical créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Dossier médical créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MedicalRecordResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicalRecordRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/patient/{patientId}', name: 'api_medical_records_get_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère le dossier médical ouvert ou le plus récent d’un patient.',
        summary: 'Afficher le dossier médical d’un patient'
    )]
    #[OA\Response(response: 200, description: 'Dossier médical récupéré avec succès')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_records_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’un dossier médical spécifique par son identifiant.',
        summary: 'Afficher un dossier médical'
    )]
    #[OA\Response(
        response: 200,
        description: 'Dossier médical récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'data', ref: new Model(type: MedicalRecordResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Dossier médical introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getOne(string $id): JsonResponse
    {
        $feedback = $this->service->getOne($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_records_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Met à jour les informations d’un dossier médical existant (la fermeture du dossier requiert le rôle PRIMARY_CLINICIAN).',
        summary: 'Mettre à jour un dossier médical'
    )]
    #[OA\RequestBody(
        description: 'Paramètres mis à jour du dossier médical',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicalRecordRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Dossier médical mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Dossier médical mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MedicalRecordResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Accès refusé : Seul le clinicien principal (PRIMARY_CLINICIAN) peut fermer ce dossier')]
    #[OA\Response(response: 404, description: 'Dossier médical introuvable')]
    public function update(string $id, #[MapRequestPayload] MedicalRecordRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_records_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime ou archive un dossier médical existant.',
        summary: 'Supprimer un dossier médical'
    )]
    #[OA\Response(
        response: 200,
        description: 'Dossier médical supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Dossier médical supprimé avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Dossier médical introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
