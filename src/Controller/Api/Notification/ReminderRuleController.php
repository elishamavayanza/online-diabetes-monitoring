<?php

namespace App\Controller\Api\Notification;

use App\DTO\Request\Notification\ReminderRuleRequestDTO;
use App\DTO\Response\Notification\ReminderRuleResponseDTO;
use App\Service\Notification\ReminderRuleService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reminder-rules')]
#[OA\Tag(name: 'Notification - Reminder Rules', description: 'Gestion des règles de rappel automatique')]
class ReminderRuleController extends AbstractController
{
    public function __construct(
        private readonly ReminderRuleService $service
    ) {}

    #[Route('', name: 'api_reminder_rules_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une règle de rappel',
        description: 'Permet de configurer une règle de rappel automatisée pour un patient (basée sur une expression Cron).'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la règle de rappel',
        content: new OA\JsonContent(
            ref: new Model(type: ReminderRuleRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Règle de rappel créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Règle de rappel créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: ReminderRuleResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] ReminderRuleRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
