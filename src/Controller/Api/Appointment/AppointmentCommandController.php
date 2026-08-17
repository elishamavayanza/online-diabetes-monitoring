<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\DTO\Request\Appointment\AppointmentRescheduleRequestDTO;
use App\Service\Appointment\AppointmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointments')]
#[OA\Tag(
    name: 'Appointments',
    description: 'Actions et gestion des rendez-vous')]
class AppointmentCommandController extends AbstractController
{
    public function __construct(
        private readonly AppointmentService $service
    ) {}

    #[Route('', name: 'api_appointments_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de planifier un rendez-vous.',
        summary: 'Créer un nouveau rendez-vous médical'
    )]
    #[OA\RequestBody(
        description: 'Données nécessaires à la création du rendez-vous',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AppointmentRequestDTO::class))
    )]
    #[OA\Response(response: 201, description: 'Rendez-vous créé avec succès')]
    #[OA\Response(response: 400, description: 'Erreur de validation')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    public function create(
        #[MapRequestPayload] AppointmentRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;
        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_appointments_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier un rendez-vous existant.',
        summary: 'Mettre à jour un rendez-vous médical'
    )]
    #[OA\RequestBody(
        description: 'Données mises à jour du rendez-vous',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AppointmentRequestDTO::class))
    )]
    #[OA\Response(response: 200, description: 'Rendez-vous mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Erreur de validation')]
    #[OA\Response(response: 404, description: 'Rendez-vous introuvable')]
    public function update(
        int $id,
        #[MapRequestPayload] AppointmentRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->update($id, $dto);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/{id}/cancel', name: 'api_appointments_cancel', methods: ['PATCH', 'POST'])]
    #[OA\Patch(
        description: "Permet d'annuler un rendez-vous médical.",
        summary: "Annuler un rendez-vous"
    )]
    #[OA\Response(response: 200, description: 'Rendez-vous annulé avec succès')]
    #[OA\Response(response: 404, description: 'Rendez-vous introuvable')]
    public function cancel(int $id): JsonResponse
    {
        $feedback = $this->service->cancel($id);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/{id}/confirm', name: 'api_appointments_confirm', methods: ['PATCH', 'POST'])]
    #[OA\Patch(
        description: "Permet de confirmer un rendez-vous médical.",
        summary: "Confirmer un rendez-vous"
    )]
    #[OA\Response(response: 200, description: 'Rendez-vous confirmé avec succès')]
    #[OA\Response(response: 404, description: 'Rendez-vous introuvable')]
    public function confirm(int $id): JsonResponse
    {
        $feedback = $this->service->confirm($id);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/{id}/reschedule', name: 'api_appointments_reschedule', methods: ['PATCH', 'POST'])]
    #[OA\Patch(
        description: "Permet de demander le report d'un rendez-vous avec une nouvelle date.",
        summary: "Demander le report d'un rendez-vous"
    )]
    #[OA\RequestBody(
        description: 'Nouvelle date souhaitée',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AppointmentRescheduleRequestDTO::class))
    )]
    #[OA\Response(response: 200, description: 'Demande de report enregistrée avec succès')]
    #[OA\Response(response: 404, description: 'Rendez-vous introuvable')]
    public function requestReschedule(
        int $id,
        #[MapRequestPayload] AppointmentRescheduleRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->requestReschedule($id, $dto->scheduledAt);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_appointments_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer (soft-delete) un rendez-vous médical.',
        summary: 'Supprimer un rendez-vous'
    )]
    #[OA\Response(response: 200, description: 'Rendez-vous supprimé avec succès')]
    #[OA\Response(response: 404, description: 'Rendez-vous introuvable')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }
}
