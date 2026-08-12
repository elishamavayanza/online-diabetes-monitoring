<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\DTO\Response\Healthcare\HealthcareOrganizationResponseDTO;
use App\Service\Healthcare\HealthcareOrganizationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/healthcare-organizations')]
#[OA\Tag(name: 'Healthcare - Organizations', description: 'Gestion des organisations et réseaux de santé')]
class HealthcareOrganizationController extends AbstractController
{
    public function __construct(
        private readonly HealthcareOrganizationService $service
    ) {}

    #[Route('', name: 'api_healthcare_organizations_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une organisation de santé',
        description: 'Permet d’enregistrer une nouvelle entité ou structure organisationnelle de santé dans le système.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’organisation de santé',
        content: new OA\JsonContent(
            ref: new Model(type: HealthcareOrganizationRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Organisation de santé créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Organisation de santé créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareOrganizationResponseDTO::class))
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
    public function create(#[MapRequestPayload] HealthcareOrganizationRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
