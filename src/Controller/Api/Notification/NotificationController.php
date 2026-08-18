<?php

namespace App\Controller\Api\Notification;

use App\DTO\Request\Notification\NotificationRequestDTO;
use App\DTO\Response\Notification\NotificationResponseDTO;
use App\Entity\Identity\User;
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

    #[Route('/type/{type}', name: 'api_notifications_get_by_type', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste des notifications filtrées par type.',
        summary: 'Obtenir les notifications par type'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Notifications récupérées avec succès.'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: NotificationResponseDTO::class)))
            ]
        )
    )]
    public function getByType(string $type): JsonResponse
    {
        $feedback = $this->service->getByType($type);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/me', name: 'api_notifications_get_my', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère les notifications destinées à l utilisateur connecté (personnelles, de ses organisations, et globales).',
        summary: 'Obtenir mes notifications'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des notifications récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Notifications récupérées avec succès.'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: NotificationResponseDTO::class)))
            ]
        )
    )]
    public function getMyNotifications(): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return $this->json(['error' => true, 'message' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $feedback = $this->service->getForUser($currentUser);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_notifications_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Envoie une notification selon sa portée (USER, ORGANIZATION, GLOBAL).',
        summary: 'Créer une ou plusieurs notifications'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la notification. Le champ `scope` détermine les destinataires.',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: NotificationRequestDTO::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Notification(s) créée(s) avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: '150 notifications envoyées avec succès.'),
                new OA\Property(property: 'data', type: 'null')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides ou règles de scope non respectées')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Droits insuffisants')]
    public function create(#[MapRequestPayload] NotificationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_notifications_update', methods: ['PUT'])]
    #[OA\Put(
        description: 'Met à jour une notification existante.',
        summary: 'Mettre à jour une notification'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: NotificationRequestDTO::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Notification mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Notification mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: NotificationResponseDTO::class))
            ]
        )
    )]
    public function update(string $id, #[MapRequestPayload] NotificationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_notifications_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Supprime une notification par son identifiant.',
        summary: 'Supprimer une notification'
    )]
    #[OA\Response(
        response: 200,
        description: 'Notification supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Notification supprimée avec succès.'),
                new OA\Property(property: 'data', type: 'null')
            ]
        )
    )]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
