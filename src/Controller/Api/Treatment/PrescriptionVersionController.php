<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionVersionRequestDTO;
use App\Service\Treatment\PrescriptionVersionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescription-versions')]
#[OA\Tag(name: 'Treatment - Prescription Versions', description: 'Gestion de l’historique et des versions de prescriptions')]
class PrescriptionVersionController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionVersionService $service
    ) {}

    #[Route('', name: 'api_prescription_versions_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une version de prescription',
        description: 'Permet d’archiver un instantané ou une version modifiée d’une prescription.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la version',
        content: new OA\JsonContent(
            required: ['prescriptionId', 'versionNumber', 'data', 'modifiedById', 'modifiedAt'],
            properties: [
                new OA\Property(property: 'prescriptionId', type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription'),
                new OA\Property(property: 'versionNumber', type: 'integer', minimum: 1, example: 2, description: 'Numéro de version'),
                new OA\Property(property: 'changesSummary', type: 'string', maxLength: 5000, nullable: true, example: 'Ajustement de la posologie du soir.', description: 'Résumé des modifications'),
                new OA\Property(property: 'data', type: 'object', example: ['key' => 'value'], description: 'Données sérialisées de la version'),
                new OA\Property(property: 'modifiedById', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’auteur de la modification'),
                new OA\Property(property: 'modifiedAt', type: 'string', format: 'date-time', example: '2026-08-10T11:00:00Z', description: 'Date de la modification')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Version de prescription créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Version enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Treatment\PrescriptionVersionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionVersionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
