<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\OrganizationMembershipRequestDTO;
use App\DTO\Response\Healthcare\OrganizationMembershipResponseDTO;
use App\Service\Healthcare\OrganizationMembershipService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/organization-memberships')]
#[OA\Tag(name: 'Healthcare - Organization Memberships', description: 'Gestion des adhésions et rattachements des utilisateurs aux organisations de santé')]
class OrganizationMembershipController extends AbstractController
{
    public function __construct(
        private readonly OrganizationMembershipService $service
    ) {}

    #[Route('/organization/{organizationId}/users/all', name: 'api_organization_memberships_list_users', methods: ['GET'], requirements: ['organization' => '.+'])]
    #[OA\Get(
        description: 'Récupère la liste de toutes les personnes (utilisateurs) rattachées à une organisation spécifique.',
        summary: 'Lister toutes les personnes d’une organisation'
    )]
    #[OA\Parameter(
        name: 'organizationId',
        description: "L'identifiant de l'organisation de santé",
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Personnes récupérées avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: "Liste de toutes les personnes de l'organisation récupérée avec succès."),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: OrganizationMembershipResponseDTO::class)))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Organisation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    public function listUsers(string $organizationId, \App\Repository\Healthcare\HealthcareOrganizationRepository $organizationRepository): JsonResponse
    {
        $organization = $organizationRepository->find($organizationId);
        if (!$organization) {
            return $this->json([
                'status' => Response::HTTP_NOT_FOUND,
                'error' => true,
                'message' => 'Organisation de santé introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        $feedback = $this->service->getAllUsersForOrganization($organization);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/organization/{organizationId}/patients/all', name: 'api_organization_memberships_list_patients', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les patients rattachés à une organisation spécifique.',
        summary: 'Lister tous les patients d’une organisation'
    )]
    #[OA\Parameter(
        name: 'organizationId',
        description: "L'identifiant de l'organisation de santé",
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Patients récupérés avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: "Liste de tous les patients de l'organisation récupérée avec succès."),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: OrganizationMembershipResponseDTO::class)))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Organisation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    public function listPatients(string $organizationId, \App\Repository\Healthcare\HealthcareOrganizationRepository $organizationRepository): JsonResponse
    {
        $organization = $organizationRepository->find($organizationId);
        if (!$organization) {
            return $this->json([
                'status' => Response::HTTP_NOT_FOUND,
                'error' => true,
                'message' => 'Organisation de santé introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        $feedback = $this->service->getAllPatientsForOrganization($organization);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_organization_memberships_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de rattacher un utilisateur (professionnel ou personnel) à une organisation, un établissement ou un département de santé.',
        summary: 'Créer une adhésion à une organisation'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’adhésion à l’organisation',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: OrganizationMembershipRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Adhésion créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Adhésion créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: OrganizationMembershipResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] OrganizationMembershipRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_organization_memberships_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les détails d’une adhésion spécifique par son identifiant.',
        summary: 'Afficher une adhésion'
    )]
    #[OA\Response(
        response: 200,
        description: 'Adhésion récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Adhésion récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: OrganizationMembershipResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Adhésion introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getById(string $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_organization_memberships_update_put', methods: ['PUT'])]
    #[Route('/{id}', name: 'api_organization_memberships_update_patch', methods: ['PATCH'])]
    #[OA\Put(
        description: 'Met à jour complètement ou partiellement une adhésion existante.',
        summary: 'Mettre à jour une adhésion'
    )]
    #[OA\Patch(
        description: 'Met à jour partiellement une adhésion existante.',
        summary: 'Mettre à jour partiellement une adhésion'
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de l’adhésion',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: OrganizationMembershipRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Adhésion mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Adhésion mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: OrganizationMembershipResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Adhésion introuvable')]
    public function update(string $id, #[MapRequestPayload] OrganizationMembershipRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_organization_memberships_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime un rattachement ou une adhésion existante.',
        summary: 'Supprimer une adhésion'
    )]
    #[OA\Response(
        response: 200,
        description: 'Adhésion supprimée avec succès'
    )]
    #[OA\Response(response: 404, description: 'Adhésion introuvable')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
