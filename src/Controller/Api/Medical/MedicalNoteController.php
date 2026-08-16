<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\MedicalNoteRequestDTO;
use App\DTO\Response\Medical\MedicalNoteResponseDTO;
use App\Service\Medical\MedicalNoteService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-notes')]
#[OA\Tag(name: 'Medical - Notes', description: 'Gestion des notes médicales')]
class MedicalNoteController extends AbstractController
{
    public function __construct(
        private readonly MedicalNoteService $service
    ) {}

    #[Route('', name: 'api_medical_notes_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet à un professionnel de santé d’ajouter une note clinique au dossier médical d’un patient.',
        summary: 'Créer une note médicale'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la note médicale',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicalNoteRequestDTO::class)
        )
    )]
    #[OA\Response(response: 201, description: 'Note médicale créée avec succès')]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicalNoteRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/record/{medicalRecordId}', name: 'api_medical_notes_list_by_record', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de lister toutes les notes médicales liées à un dossier médical spécifique.',
        summary: 'Lister les notes d’un dossier médical'
    )]
    #[OA\Parameter(name: 'medicalRecordId', description: 'Identifiant du dossier médical', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Dossier médical non trouvé')]
    public function listByRecord(string $medicalRecordId): JsonResponse
    {
        $feedback = $this->service->getByMedicalRecord($medicalRecordId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_notes_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer une note médicale spécifique par son ID.',
        summary: 'Récupérer une note médicale par ID'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant de la note', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Note récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Note non trouvée')]
    public function getById(string $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_notes_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une note médicale existante.',
        summary: 'Modifier une note médicale'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant de la note', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MedicalNoteRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Note mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Note non trouvée')]
    public function update(string $id, #[MapRequestPayload] MedicalNoteRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_notes_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une note médicale.',
        summary: 'Supprimer une note médicale'
    )]
    #[OA\Parameter(name: 'id', description: 'Identifiant de la note', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Note supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Note non trouvée')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
