<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\DTO\Response\Medical\DiagnosisResponseDTO;
use App\Service\Medical\DiagnosisService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/diagnoses')]
#[OA\Tag(name: 'Medical - Diagnoses', description: 'Gestion des diagnostics médicaux')]
class DiagnosisController extends AbstractController
{
    public function __construct(
        private readonly DiagnosisService $service
    ) {}

    #[Route('', name: 'api_diagnoses_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un diagnostic médical',
        description: 'Permet à un professionnel de santé d’établir et d’enregistrer un diagnostic pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du diagnostic',
        content: new OA\JsonContent(
            ref: new Model(type: DiagnosisRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Diagnostic créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Diagnostic créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DiagnosisResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] DiagnosisRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
