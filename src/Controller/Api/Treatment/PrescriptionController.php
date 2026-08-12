<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionRequestDTO;
use App\DTO\Response\Treatment\PrescriptionResponseDTO;
use App\Service\Treatment\PrescriptionService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescriptions')]
#[OA\Tag(name: 'Treatment - Prescriptions', description: 'Gestion des ordonnances et prescriptions médicales')]
class PrescriptionController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionService $service
    ) {}

    #[Route('', name: 'api_prescriptions_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une prescription',
        description: 'Permet d’émettre une nouvelle prescription médicale pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la prescription',
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Prescription créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prescription créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
