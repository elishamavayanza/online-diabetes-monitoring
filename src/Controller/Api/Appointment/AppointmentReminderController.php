<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentReminderRequestDTO;
use App\DTO\Response\Appointment\AppointmentReminderResponseDTO;
use App\Service\Appointment\AppointmentReminderService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointment-reminders')]
#[OA\Tag(name: 'Appointment Reminders', description: 'Gestion des rappels de rendez-vous automatisés (SMS, Email, Notification Push)')]
class AppointmentReminderController extends AbstractController
{
    public function __construct(
        private readonly AppointmentReminderService $service
    ) {}

    #[Route('', name: 'api_appointment_reminders_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Programmer un rappel de rendez-vous',
        description: 'Permet de planifier l’envoi d’un rappel de rendez-vous via un canal spécifique (ex: SMS, EMAIL) pour un rendez-vous donné.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de configuration du rappel',
        content: new OA\JsonContent(
            required: ['appointmentId', 'channel', 'scheduledFor'],
            properties: [
                new OA\Property(property: 'appointmentId', type: 'string', format: 'uuid', example: '4a613328-98e3-4d64-8898-0c06a3861c8f', description: 'Identifiant unique du rendez-vous associé'),
                new OA\Property(property: 'channel', type: 'string', example: 'SMS', description: 'Canal de diffusion du rappel (SMS, EMAIL, PUSH)'),
                new OA\Property(property: 'scheduledFor', type: 'string', format: 'date-time', example: '2026-08-14T09:00:00Z', description: 'Date et heure prévues pour l’envoi du rappel'),
                new OA\Property(property: 'sentAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date et heure effective d’envoi (généralement nul à la création)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Rappel programmé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Rappel programmé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Appointment\AppointmentReminderResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides ou rendez-vous introuvable',
        content: new OA\JsonContent(
            example: [
                'status' => 400,
                'error' => true,
                'message' => 'Rendez-vous introuvable.',
                'data' => null
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] AppointmentReminderRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
