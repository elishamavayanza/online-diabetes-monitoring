<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\DTO\Response\Treatment\PrescriptionItemResponseDTO;
use App\Service\Treatment\PrescriptionItemService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescription-items')]
#[OA\Tag(name: 'Treatment - Prescription Items', description: 'Gestion des lignes d’éléments de prescription')]
class PrescriptionItemController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionItemService $service
    ) {}

    #[Route('', name: 'api_prescription_items_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajouter un élément à une prescription',
        description: 'Permet d’associer un médicament et sa posologie à une ordonnance existante.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’élément de prescription',
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionItemRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Élément de prescription créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément ajouté avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
