<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\Service\Medical\LaboratoryResultService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/laboratory-results')]
#[OA\Tag(name: 'Medical - Laboratory Results', description: 'Gestion des résultats de laboratoire')]
class LaboratoryResultController extends AbstractController
{
    public function __construct(
        private readonly LaboratoryResultService $service
    ) {}

    #[Route('', name: 'api_laboratory_results_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Ajouter un résultat de laboratoire',
        description: 'Permet d’enregistrer un nouveau résultat d’examen de laboratoire pour un patient.'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du résultat de laboratoire',
        content: new OA\JsonContent(
            required: ['testName'],
            properties: [
                new OA\Property(property: 'testName', type: 'string', maxLength: 150, example: 'Glycémie à jeun / Bilan lipidique', description: 'Nom de l’examen'),
                new OA\Property(property: 'fileUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/labs/result-123.pdf', description: 'URL du fichier de résultat'),
                new OA\Property(property: 'labName', type: 'string', maxLength: 150, nullable: true, example: 'Laboratoire Central Goma', description: 'Nom du laboratoire')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Résultat de laboratoire ajouté avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Résultat de laboratoire ajouté avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Medical\LaboratoryResultResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] LaboratoryResultRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
