<?php

namespace App\Controller\Api\Identity;

use App\DTO\Feedback;
use App\Entity\Identity\User;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\Common\PresenceService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users')]
#[OA\Tag(
    name: 'Identity - Users',
    description: 'Gestion des comptes utilisateurs'
)]
class PresenceController extends AbstractController
{
    public function __construct(
        private readonly PresenceService $presenceService,
        private readonly UserRepository $userRepository,
        private readonly SecurityServiceInterface $securityService,
    ) {
    }

    #[Route('/{id}/presence', name: 'api_users_presence', methods: ['GET'])]
    #[OA\Get(
        description: 'Indique si l’utilisateur est actuellement « en ligne » (activité détectée dans les dernières secondes).',
        summary: 'Vérifier la présence d’un utilisateur'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID de l\'utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'État de présence récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Présence récupérée avec succès.'),
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(property: 'isOnline', type: 'boolean', example: true),
                    ],
                    type: 'object'
                ),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Permission insuffisante')]
    #[OA\Response(response: 404, description: 'Utilisateur introuvable')]
    public function presence(string $id): JsonResponse
    {
        $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);

        $feedback = new Feedback();

        $user = $this->userRepository->find($id);

        if (!$user instanceof User) {
            $feedback->addError('id', 'Utilisateur introuvable.');

            return $this->json($feedback, Response::HTTP_NOT_FOUND);
        }

        $feedback->setData([
            'isOnline' => $this->presenceService->isOnline($user),
        ]);

        return $this->json($feedback, Response::HTTP_OK);
    }
}