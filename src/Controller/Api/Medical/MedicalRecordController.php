<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\MedicalRecordRequestDTO;
use App\DTO\Response\Medical\MedicalRecordResponseDTO;
use App\Service\Medical\MedicalRecordService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-records')]
#[OA\Tag(name: 'Medical - Records', description: 'Gestion des dossiers médicaux')]
class MedicalRecordController extends AbstractController
{
    public function __construct(
        private readonly MedicalRecordService $service
    ) {}

    #[Route('', name: 'api_medical_records_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un dossier médical',
        description: 'Permet d’ouvrir un nouveau dossier médical pour un patient dans une organisation de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du dossier médical',
        content: new OA\JsonContent(
            ref: new Model(type: MedicalRecordRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Dossier médical créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Dossier médical créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: MedicalRecordResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicalRecordRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
