<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\AssignRoleRequestDTO;
use App\Service\Identity\UserRoleService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users')]
#[OA\Tag(
    name: 'Identity - User Roles',
    description: 'Gestion des rôles de sécurité des utilisateurs'
)]
class UserRoleController extends AbstractController
{
    public function __construct(
        private readonly UserRoleService $userRoleService
    ) {}

    #[Route(
        '/{id}/roles',
        name: 'api_users_assign_role',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    #[OA\Post(
        description: 'Permet d’attribuer un rôle de sécurité à un utilisateur existant.',
        summary: 'Attribuer un rôle à un utilisateur'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant numérique de l’utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer',
            format: 'int64',
            example: 6
        )
    )]
    #[OA\RequestBody(
        description: 'Rôle à attribuer',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: AssignRoleRequestDTO::class
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Rôle attribué avec succès'
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    #[OA\Response(
        response: 403,
        description: 'Permission insuffisante'
    )]
    #[OA\Response(
        response: 404,
        description: 'Utilisateur introuvable'
    )]
    public function assignRole(
        int $id,
        #[MapRequestPayload] AssignRoleRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->userRoleService->assignRole(
            $id,
            $dto
        );

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
