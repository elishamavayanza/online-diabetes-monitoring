<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\DTO\Response\Healthcare\HealthcareFacilityResponseDTO;
use App\Service\Healthcare\HealthcareFacilityService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ref: new Model(type: HealthcareFacilityRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: HealthcareFacilityResponseDTO::class))
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
