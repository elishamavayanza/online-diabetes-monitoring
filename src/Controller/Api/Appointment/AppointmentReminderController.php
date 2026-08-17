<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentReminderRequestDTO;
use App\DTO\Response\Appointment\AppointmentReminderResponseDTO;
use App\Service\Appointment\AppointmentReminderService;
use Nelmio\ApiDocBundle\Attribute\Model;
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

    #[Route('/{id}', name: 'api_appointment_reminders_get', methods: ['GET'])]
    #[OA\Get(
        description: "Permet de récupérer les détails d'un rappel spécifique par son ID.",
        summary: "Afficher un rappel de rendez-vous"
    )]
    #[OA\Response(
        response: 200,
        description: 'Rappel récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Rappel récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: AppointmentReminderResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Rappel introuvable')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/appointment/{appointmentId}', name: 'api_appointment_reminders_by_appointment', methods: ['GET'])]
    #[OA\Get(
        description: "Permet de lister tous les rappels programmés pour un rendez-vous particulier.",
        summary: "Lister les rappels d'un rendez-vous"
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'flush', type: 'string', example: 'Succès d\'exécution de l\'opération', nullable: true),
                new OA\Property(property: 'flushDescription', type: 'string', example: null, nullable: true),
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'errors', type: 'object', example: []),
                new OA\Property(property: 'warnings', type: 'object', example: []),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/AppointmentReminderResponseDTO')
                )
            ]
        )
    )]
    public function getByAppointment(int $appointmentId): JsonResponse
    {
        $feedback = $this->service->getByAppointment($appointmentId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_appointment_reminders_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de planifier l’envoi d’un rappel de rendez-vous via un canal spécifique (ex: SMS, EMAIL) pour un rendez-vous donné.',
        summary: 'Programmer un rappel de rendez-vous'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de configuration du rappel',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: AppointmentReminderRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: AppointmentReminderResponseDTO::class))
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
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_appointment_reminders_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: "Permet de modifier un rappel de rendez-vous existant.",
        summary: "Mettre à jour un rappel"
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres du rappel',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: AppointmentReminderRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Rappel mis à jour avec succès'
    )]
    public function update(int $id, #[MapRequestPayload] AppointmentReminderRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_appointment_reminders_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Permet de supprimer un rappel de rendez-vous programmé.",
        summary: "Supprimer un rappel"
    )]
    #[OA\Response(
        response: 200,
        description: 'Rappel supprimé avec succès'
    )]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
