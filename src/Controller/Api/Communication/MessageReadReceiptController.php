<?php

namespace App\Controller\Api\Communication;

use App\DTO\Request\Communication\MessageReadReceiptRequestDTO;
use App\DTO\Response\Communication\MessageReadReceiptResponseDTO;
use App\Service\Communication\MessageReadReceiptService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/message-read-receipts')]
#[OA\Tag(name: 'Communication - Message Read Receipts', description: 'Gestion des accusés de lecture des messages')]
class MessageReadReceiptController extends AbstractController
{
    public function __construct(
        private readonly MessageReadReceiptService $service
    ) {}

    #[Route('', name: 'api_message_read_receipts_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer l’accusé de lecture d’un message par un participant spécifique de la conversation.',
        summary: 'Marquer un message comme lu'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’accusé de lecture',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MessageReadReceiptRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Accusé de lecture enregistré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Accusé de lecture enregistré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MessageReadReceiptResponseDTO::class))
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
    public function create(#[MapRequestPayload] MessageReadReceiptRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
