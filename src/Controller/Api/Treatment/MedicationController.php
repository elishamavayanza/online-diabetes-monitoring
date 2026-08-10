<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\Service\Treatment\MedicationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medications')]
#[OA\Tag(name: 'Treatment - Medications', description: 'Gestion des médicaments')]
class MedicationController extends AbstractController
{
    public function __construct(
        private readonly MedicationService $service
    ) {}

    #[Route('', name: 'api_medications_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un médicament',
        description: 'Permet d’enregistrer un nouveau médicament dans le catalogue.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du médicament',
        content: new OA\JsonContent(
            required: ['name', 'category'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Paracétamol 500mg', description: 'Nom du médicament'),
                new OA\Property(property: 'category', type: 'string', example: 'ANALGESIC', description: 'Catégorie du médicament'),
                new OA\Property(property: 'description', type: 'string', maxLength: 5000, nullable: true, example: 'Antalgique et antipyrétique.', description: 'Description'),
                new OA\Property(property: 'insulinLevel', type: 'integer', minimum: 0, nullable: true, example: 0, description: 'Niveau d’insuline associé (le cas échéant)'),
                new OA\Property(property: 'manufacturer', type: 'string', maxLength: 150, nullable: true, example: 'PharmaLab', description: 'Fabricant')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Médicament créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Médicament créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Treatment\MedicationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
