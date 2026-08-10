<?php

namespace App\Controller\Api\Notification;

use App\DTO\Request\Notification\NotificationRequestDTO;
use App\Service\Notification\NotificationService;
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
            required: ['userId', 'type', 'title', 'body', 'channel'],
            properties: [
                new OA\Property(property: 'userId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’utilisateur destinataire'),
                new OA\Property(property: 'type', type: 'string', example: 'ALERT', description: 'Type de notification (ex: ALERT, REMINDER, INFO)'),
                new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Rappel de glycémie', description: 'Titre de la notification'),
                new OA\Property(property: 'body', type: 'string', example: 'Il est l’heure de mesurer votre glycémie à jeun.', description: 'Contenu du message'),
                new OA\Property(property: 'channel', type: 'string', example: 'PUSH', description: 'Canal d’envoi (ex: PUSH, SMS, EMAIL)'),
                new OA\Property(property: 'readAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de lecture (optionnel)'),
                new OA\Property(property: 'relatedEntityType', type: 'string', maxLength: 150, nullable: true, example: 'BloodGlucoseMeasurement', description: 'Type d’entité liée'),
                new OA\Property(property: 'relatedEntityId', type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’entité liée')
            ],
            type: 'object'
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
                new OA\Property(property: 'data', ref: '#/components/schemas/NotificationResponseDTO')
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
