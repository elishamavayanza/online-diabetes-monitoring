<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\OrganizationMembershipRequestDTO;
use App\Service\Healthcare\OrganizationMembershipService;
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

    #[Route('', name: 'api_organization_memberships_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une adhésion à une organisation',
        description: 'Permet de rattacher un utilisateur (professionnel ou personnel) à une organisation, un établissement ou un département de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’adhésion à l’organisation',
        content: new OA\JsonContent(
            required: ['userId', 'organizationId', 'startDate', 'status'],
            properties: [
                new OA\Property(property: 'userId', type: 'string', format: 'uuid', example: '7b224119-12f4-4b53-9912-1f83c2748a12', description: 'Identifiant unique de l’utilisateur'),
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique de l’organisation'),
                new OA\Property(property: 'facilityId', type: 'string', format: 'uuid', nullable: true, example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant unique de l’établissement de santé (optionnel)'),
                new OA\Property(property: 'departmentId', type: 'string', format: 'uuid', nullable: true, example: '77cc88bb-11aa-4333-9988-123456789abc', description: 'Identifiant unique du département médical (optionnel)'),
                new OA\Property(property: 'startDate', type: 'string', format: 'date', example: '2026-08-10', description: 'Date de début de l’adhésion'),
                new OA\Property(property: 'endDate', type: 'string', format: 'date', nullable: true, example: null, description: 'Date de fin de l’adhésion (optionnelle)'),
                new OA\Property(property: 'status', type: 'string', example: 'ACTIVE', description: 'Statut de l’adhésion')
            ],
            type: 'object'
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
                new OA\Property(property: 'data', ref: '#/components/schemas/OrganizationMembershipResponseDTO')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données de la requête invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] OrganizationMembershipRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
