<?php

namespace App\Controller\Api\Communication;

use App\DTO\Response\Communication\ConversationSummaryResponseDTO;
use App\DTO\Response\Communication\MessageDetailResponseDTO;
use App\Service\Communication\ConversationQueryService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/conversations')]
#[OA\Tag(name: 'Communication - Conversations')]
class ConversationQueryController extends AbstractController
{
    public function __construct(
        private readonly ConversationQueryService $service,
    ) {}

    #[Route('', name: 'api_conversations_list', methods: ['GET'])]
    #[OA\Get(summary: 'Lister mes conversations')]
    #[OA\Response(
        response: 200,
        description: 'Conversations récupérées avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: ConversationSummaryResponseDTO::class))),
            ],
        ),
    )]
    public function list(): JsonResponse
    {
        $feedback = $this->service->getMine();

        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/{id}/messages', name: 'api_conversations_messages_list', methods: ['GET'])]
    #[OA\Get(summary: 'Lister les messages d’une conversation')]
    #[OA\Response(
        response: 200,
        description: 'Messages récupérés avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: MessageDetailResponseDTO::class))),
            ],
        ),
    )]
    public function messages(string $id): JsonResponse
    {
        $feedback = $this->service->getMessages($id);

        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }
}
