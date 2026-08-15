<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\AllergyRequestDTO;
use App\DTO\Response\Patient\AllergyResponseDTO;
use App\Service\Patient\AllergyService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/allergies')]
#[OA\Tag(name: 'Patient - Allergies', description: 'Gestion des allergies des patients')]
class AllergyController extends AbstractController
{
    public function __construct(
        private readonly AllergyService $allergyService
    ) {}

    #[Route('', name: 'api_allergies_create', methods: ['POST'])]
    #[OA\Post(description: 'Permet d’enregistrer une nouvelle allergie pour un patient.', summary: 'Créer une allergie')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: AllergyRequestDTO::class)))]
    #[OA\Response(response: 201, description: 'Allergie créée avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] AllergyRequestDTO $dto): JsonResponse
    {
        $feedback = $this->allergyService->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_allergies_get', methods: ['GET'])]
    #[OA\Get(description: 'Récupère une allergie spécifique par son ID.', summary: 'Afficher une allergie')]
    #[OA\Response(response: 200, description: 'Allergie récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Allergie introuvable')]
    public function getOne(string $id): JsonResponse
    {
        $feedback = $this->allergyService->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/patient/{patientId}', name: 'api_allergies_by_patient', methods: ['GET'])]
    #[OA\Get(description: 'Récupère toutes les allergies d’un patient.', summary: 'Lister les allergies d’un patient')]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    public function getByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->allergyService->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_allergies_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(description: 'Met à jour une allergie existante.', summary: 'Modifier une allergie')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: AllergyRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Allergie mise à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    public function update(string $id, #[MapRequestPayload] AllergyRequestDTO $dto): JsonResponse
    {
        $feedback = $this->allergyService->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_allergies_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime une allergie.', summary: 'Supprimer une allergie')]
    #[OA\Response(response: 200, description: 'Allergie supprimée avec succès')]
    #[OA\Response(response: 404, description: 'Allergie introuvable')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->allergyService->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
