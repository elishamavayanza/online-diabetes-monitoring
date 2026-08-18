<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageRequestDTO;
use App\DTO\Response\Communication\MessageResponseDTO;
use App\Service\Communication\MessageService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/messages')]
#[OA\Tag(name: 'Communication - Messages', description: 'Gestion des messages échangés au sein des fils de discussion')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly MessageService $service
    ) {}

    #[Route('', name: 'api_messages_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’émettre et d’enregistrer un message textuel à l’intérieur d’une conversation existante.',
        summary: 'Envoyer un nouveau message'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du message à envoyer',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MessageRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Message envoyé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Message envoyé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MessageResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MessageRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_messages_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’un message spécifique par son identifiant.',
        summary: 'Récupérer un message'
    )]
    #[OA\Response(
        response: 200,
        description: 'Message récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Message récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MessageResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Message introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function get(string $id): JsonResponse
    {
        $feedback = $this->service->get($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_messages_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de mettre à jour un message existant.',
        summary: 'Mettre à jour un message'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de mise à jour du message',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MessageRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Message mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Message mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MessageResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Message introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(string $id, #[MapRequestPayload] MessageRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un message par son identifiant.',
        summary: 'Supprimer un message'
    )]
    #[OA\Response(
        response: 200,
        description: 'Message supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Message supprimé avec succès.'),
                new OA\Property(property: 'data', type: 'null', example: null)
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Message introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
