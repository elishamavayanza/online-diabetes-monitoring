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



    #[Route('', name: 'api_reminder_rules_list', methods: ['GET'])]
    #[OA\Get(description: 'Récupérer la liste des règles de rappel', summary: 'Lister les règles')]
    #[OA\Response(response: 200, description: 'Succès')]
    public function list(): JsonResponse
    {
        $feedback = $this->service->findAll();
        return $this->json($feedback, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_reminder_rules_get', methods: ['GET'])]
    #[OA\Get(description: 'Récupérer une règle de rappel par son ID', summary: 'Afficher une règle')]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Règle introuvable')]
    public function getOne(int $id): JsonResponse
    {
        $feedback = $this->service->find($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_reminder_rules_create', methods: ['POST'])]
    #[OA\Post(description: 'Créer une règle de rappel', summary: 'Créer une règle de rappel')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: ReminderRuleRequestDTO::class)))]
    #[OA\Response(response: 201, description: 'Créé avec succès')]
    public function create(#[MapRequestPayload] ReminderRuleRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;
        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_reminder_rules_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(description: 'Modifier une règle de rappel', summary: 'Mettre à jour une règle')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: ReminderRuleRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Mis à jour avec succès')]
    public function update(int $id, #[MapRequestPayload] ReminderRuleRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;
        return $this->json($feedback, $status);
    }

    #[Route('/{id}/toggle', name: 'api_reminder_rules_toggle', methods: ['PATCH'])]
    #[OA\Patch(description: 'Activer ou désactiver une règle de rappel', summary: 'Activer / Désactiver')]
    #[OA\Response(response: 200, description: 'Statut modifié avec succès')]
    public function toggle(int $id): JsonResponse
    {
        $feedback = $this->service->toggle($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;
        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_reminder_rules_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprimer une règle de rappel', summary: 'Supprimer une règle')]
    #[OA\Response(response: 204, description: 'Supprimé avec succès')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;
        return $this->json($feedback, $status);
    }
}
