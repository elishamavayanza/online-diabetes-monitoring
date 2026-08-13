<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\OrganizationAdministratorRequestDTO;
use App\DTO\Response\Identity\UserResponseDTO;
use App\Service\Healthcare\OrganizationAdministratorService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/healthcare-organizations/{organizationId}/administrators')]
#[OA\Tag(name: 'Healthcare - Organization Administrators', description: 'Gestion des administrateurs d’organisation')]
class OrganizationAdministratorController extends AbstractController
{
    public function __construct(
        private readonly OrganizationAdministratorService $service
    ) {}

    #[Route('', name: 'api_healthcare_organizations_administrators_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un administrateur d’organisation',
        description: 'Crée un utilisateur avec ROLE_ADMIN et une adhésion active à l’organisation ciblée. Réservé au ROLE_ROOT.'
    )]
    #[OA\Parameter(
        name: 'organizationId',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        description: 'Identifiant de l’organisation'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: OrganizationAdministratorRequestDTO::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Administrateur créé avec succès',
        content: new OA\JsonContent(ref: new Model(type: UserResponseDTO::class))
    )]
    #[OA\Response(response: 400, description: 'Données invalides, e-mail déjà utilisé ou organisation introuvable')]
    #[OA\Response(response: 403, description: 'Réservé au ROLE_ROOT')]
    public function create(
        string $organizationId,
        #[MapRequestPayload] OrganizationAdministratorRequestDTO $dto,
    ): JsonResponse {
        $feedback = $this->service->create($organizationId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
