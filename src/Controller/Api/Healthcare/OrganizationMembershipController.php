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

    #[Route('', name: 'api_organization_memberships_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une adhésion à une organisation',
        description: 'Permet de rattacher un utilisateur (professionnel ou personnel) à une organisation, un établissement ou un département de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’adhésion à l’organisation',
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
