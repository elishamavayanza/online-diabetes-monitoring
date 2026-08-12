<?php

namespace App\Controller\Api\Notification;

use App\DTO\Request\Notification\NotificationRequestDTO;
use App\DTO\Response\Notification\NotificationResponseDTO;
use App\Service\Notification\NotificationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/notifications')]
#[OA\Tag(name: 'Notification - Notifications', description: 'Gestion des notifications utilisateur')]
class NotificationController extends AbstractController
{
    public function __construct(
        private readonly NotificationService $service
    ) {}

    #[Route('', name: 'api_notifications_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une notification',
        description: 'Permet d’envoyer et d’enregistrer une nouvelle notification pour un utilisateur.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la notification',
        content: new OA\JsonContent(
            ref: new Model(type: NotificationRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Notification créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Notification créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: NotificationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] NotificationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
