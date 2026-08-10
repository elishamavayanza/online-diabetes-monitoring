<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\Service\Healthcare\HealthcareOrganizationService;
use Nelmio\ApiDocBundle\Annotation\Model;
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
            required: ['name', 'type', 'active'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'DiabCare Health Group', description: 'Nom complet de l’organisation'),
                new OA\Property(property: 'shortName', type: 'string', maxLength: 50, nullable: true, example: 'DHG', description: 'Nom court ou acronyme'),
                new OA\Property(property: 'type', type: 'string', example: 'HOSPITAL_NETWORK', description: 'Type d’organisation de santé'),
                new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 180, nullable: true, example: 'contact@diabcare.com', description: 'Adresse e-mail principale'),
                new OA\Property(property: 'phone', type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Téléphone principal'),
                new OA\Property(property: 'website', type: 'string', format: 'uri', maxLength: 255, nullable: true, example: 'https://www.diabcare.com', description: 'Site Web institutionnel'),
                new OA\Property(property: 'logoUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/logos/dhg.png', description: 'URL du logo de l’organisation'),
                new OA\Property(
                    property: 'address',
                    type: 'object',
                    nullable: true,
                    description: 'Adresse physique du siège',
                    properties: [
                        new OA\Property(property: 'street', type: 'string', example: '12 Avenue de la Santé'),
                        new OA\Property(property: 'city', type: 'string', example: 'Goma'),
                        new OA\Property(property: 'state', type: 'string', example: 'Nord-Kivu'),
                        new OA\Property(property: 'postalCode', type: 'string', example: '00243'),
                        new OA\Property(property: 'country', type: 'string', example: 'RDC')
                    ]
                ),
                new OA\Property(property: 'active', type: 'boolean', example: true, description: 'Statut actif de l’organisation')
            ],
            type: 'object'
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
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Healthcare\HealthcareOrganizationResponseDTO::class))
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
