<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\UserCreateRequestDTO;
use App\DTO\Response\Identity\UserResponseDTO;
use App\Service\Identity\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users')]
#[OA\Tag(
    name: 'Identity - Users',
    description: 'Gestion des comptes utilisateurs'
)]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Route('', name: 'api_users_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les utilisateurs (ou administrateurs).',
        summary: 'Lister les utilisateurs'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des utilisateurs récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste des utilisateurs récupérée avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: UserResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Permission insuffisante')]
    public function list(): JsonResponse
    {
        $feedback = $this->userService->getAll();

        return $this->json(
            $feedback,
            Response::HTTP_OK
        );
    }

    #[Route('/profile', name: 'api_users_update_profile', methods: ['PUT', 'PATCH'])]
    #[OA\Put(summary: 'Modifier son propre profil utilisateur')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: UserCreateRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Profil mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Profil mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: UserResponseDTO::class))
            ]
        )
    )]
    public function updateProfile(
        #[MapRequestPayload] UserCreateRequestDTO $dto
    ): JsonResponse {
        $currentUser = $this->securityService->getCurrentUser();

        if (!$currentUser) {
            return $this->json([
                'status' => 401,
                'error' => true,
                'message' => 'Non authentifié.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $feedback = $this->userService->update((string) $currentUser->getId(), $dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_users_create', methods: ['POST'])]
    #[OA\Post(
        description: <<<'DESC'
Crée un compte utilisateur de base.

Pour le modèle actuel, un compte destiné à un patient
est représenté par l'entité Patient.

Cette opération crée uniquement les informations nécessaires
au compte et ne constitue pas la création d'un profil patient
médical complet.

Le rôle ROLE_PATIENT est attribué automatiquement.
DESC,
        summary: 'Créer un compte utilisateur'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: UserCreateRequestDTO::class
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Compte utilisateur créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    type: 'integer',
                    example: 201
                ),
                new OA\Property(
                    property: 'error',
                    type: 'boolean',
                    example: false
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Compte utilisateur créé avec succès.'
                ),
                new OA\Property(
                    property: 'data',
                    ref: new Model(
                        type: UserResponseDTO::class
                    )
                )
            ]
        )
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
    public function create(
        #[MapRequestPayload]
        UserCreateRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->userService->create($dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_CREATED;

        return $this->json(
            $feedback,
            $status
        );
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(summary: 'Modifier un utilisateur')]
    #[OA\Patch(summary: 'Modifier partiellement un utilisateur')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de l\'utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: UserCreateRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Utilisateur mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Utilisateur mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: UserResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Utilisateur introuvable')]
    public function update(
        int $id,
        #[MapRequestPayload] UserCreateRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->userService->update($id, $dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
