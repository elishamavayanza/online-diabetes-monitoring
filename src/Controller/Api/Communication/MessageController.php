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
        summary: 'Envoyer un nouveau message',
        description: 'Permet d’émettre et d’enregistrer un message textuel à l’intérieur d’une conversation existante.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du message à envoyer',
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
    #[OA\Response(
        response: 400,
        description: 'Données de la requête invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function create(#[MapRequestPayload] MessageRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
