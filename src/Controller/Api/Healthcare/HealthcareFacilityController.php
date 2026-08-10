<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\Service\Healthcare\HealthcareFacilityService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/healthcare-facilities')]
#[OA\Tag(name: 'Healthcare - Facilities', description: 'Gestion des structures et établissements de santé')]
class HealthcareFacilityController extends AbstractController
{
    public function __construct(
        private readonly HealthcareFacilityService $service
    ) {}

    #[Route('', name: 'api_healthcare_facilities_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un établissement de santé',
        description: 'Permet d’enregistrer un nouvel hôpital, clinique ou centre médical rattaché à une organisation.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’établissement de santé',
        content: new OA\JsonContent(
            required: ['organizationId', 'name'],
            properties: [
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', example: '88a123ff-44ee-4111-8899-7a6543210123', description: 'Identifiant unique de l’organisation parente'),
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Hôpital Général de Référence de DiabCare', description: 'Nom de l’établissement'),
                new OA\Property(
                    property: 'address',
                    type: 'object',
                    nullable: true,
                    description: 'Adresse physique de l’établissement',
                    properties: [
                        new OA\Property(property: 'street', type: 'string', example: '12 Avenue de la Santé'),
                        new OA\Property(property: 'city', type: 'string', example: 'Goma'),
                        new OA\Property(property: 'state', type: 'string', example: 'Nord-Kivu'),
                        new OA\Property(property: 'postalCode', type: 'string', example: '00243'),
                        new OA\Property(property: 'country', type: 'string', example: 'RDC')
                    ]
                ),
                new OA\Property(property: 'phone', type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Numéro de téléphone principal')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Établissement de santé créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Établissement de santé créé avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/HealthcareFacilityResponseDTO')
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
    public function create(#[MapRequestPayload] HealthcareFacilityRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
