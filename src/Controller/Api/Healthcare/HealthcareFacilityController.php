<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\DTO\Response\Healthcare\HealthcareFacilityResponseDTO;
use App\Service\Healthcare\HealthcareFacilityService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/healthcare-facilities')]
#[OA\Tag(name: 'Healthcare - Facilities', description: 'Gestion des structures et établissements de santé')]
class HealthcareFacilityController extends AbstractController
{
    public function __construct(
        private readonly HealthcareFacilityService $service
    ) {}


    #[Route('/organization/{organizationId}', name: 'api_healthcare_facilities_by_organization', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les établissements de santé rattachés à une organisation spécifique.',
        summary: 'Lister les établissements d’une organisation'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste des établissements récupérée avec succès.'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: HealthcareFacilityResponseDTO::class)))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Organisation introuvable')]
    public function getByOrganization(string $organizationId): JsonResponse
    {
        $feedback = $this->service->getByOrganization($organizationId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_healthcare_facilities_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer un nouvel hôpital, clinique ou centre médical rattaché à une organisation.',
        summary: 'Créer un établissement de santé'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’établissement de santé',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HealthcareFacilityRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Établissement de santé créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Établissement de santé créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareFacilityResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] HealthcareFacilityRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_healthcare_facilities_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier les informations d’un établissement de santé existant.',
        summary: 'Modifier un établissement de santé'
    )]
    #[OA\RequestBody(
        description: 'Paramètres mis à jour de l’établissement',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: HealthcareFacilityRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Établissement de santé mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Établissement de santé mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareFacilityResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Établissement introuvable')]
    public function update(string $id, #[MapRequestPayload] HealthcareFacilityRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }


    #[Route('/{id}/suspend', name: 'api_healthcare_facilities_suspend', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Permet de suspendre ou réactiver un établissement de santé.',
        summary: 'Suspendre ou réactiver un établissement'
    )]
    #[OA\Response(
        response: 200,
        description: 'Statut de l’établissement mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Statut de l’établissement mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareFacilityResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Établissement introuvable')]
    public function suspend(string $id): JsonResponse
    {
        $feedback = $this->service->toggleSuspend($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
