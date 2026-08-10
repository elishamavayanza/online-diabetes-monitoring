<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\Service\Identity\HealthcareProfessionalService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/professionals')]
#[OA\Tag(name: 'Identity - Professionals', description: 'Gestion des professionnels de santé')]
class HealthcareProfessionalController extends AbstractController
{
    public function __construct(
        private readonly HealthcareProfessionalService $professionalService
    ) {}

    #[Route('', name: 'api_professionals_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un professionnel de santé',
        description: 'Permet d’inscrire un nouveau professionnel de santé dans le système avec ses informations personnelles et professionnelles.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du professionnel de santé',
        content: new OA\JsonContent(
            required: ['email', 'password', 'firstName', 'lastName', 'gender', 'licenseNumber', 'professionalType'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 180, example: 'dr.jean@diabcare.com', description: 'Adresse e-mail unique'),
                new OA\Property(property: 'password', type: 'string', minLength: 8, example: 'SecurePassword123!', description: 'Mot de passe utilisateur'),
                new OA\Property(property: 'phone', type: 'string', maxLength: 50, nullable: true, example: '+243990000000', description: 'Téléphone'),
                new OA\Property(property: 'firstName', type: 'string', maxLength: 100, example: 'Jean', description: 'Prénom'),
                new OA\Property(property: 'lastName', type: 'string', maxLength: 100, example: 'Mukendi', description: 'Nom de famille'),
                new OA\Property(property: 'avatarUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/avatars/jean.jpg', description: 'URL de l’avatar'),
                new OA\Property(property: 'gender', type: 'string', example: 'MALE', description: 'Genre (MALE, FEMALE, OTHER)'),
                new OA\Property(property: 'locale', type: 'string', maxLength: 10, example: 'fr', description: 'Langue / Locale'),
                new OA\Property(property: 'licenseNumber', type: 'string', maxLength: 100, example: 'ORD-MED-2026-99', description: 'Numéro d’ordre / de licence professionnelle'),
                new OA\Property(property: 'professionalType', type: 'string', example: 'DOCTOR', description: 'Type de professionnel (DOCTOR, NURSE, etc.)'),
                new OA\Property(property: 'specialty', type: 'string', maxLength: 150, nullable: true, example: 'Endocrinologie et Diabétologie', description: 'Spécialité médicale'),
                new OA\Property(property: 'signatureUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/sigs/jean.png', description: 'URL de la signature électronique'),
                new OA\Property(property: 'street', type: 'string', maxLength: 255, nullable: true, example: '12 Avenue de la Paix', description: 'Rue'),
                new OA\Property(property: 'city', type: 'string', maxLength: 100, nullable: true, example: 'Goma', description: 'Ville'),
                new OA\Property(property: 'postalCode', type: 'string', maxLength: 20, nullable: true, example: '00243', description: 'Code postal'),
                new OA\Property(property: 'country', type: 'string', maxLength: 100, nullable: true, example: 'RDC', description: 'Pays')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Professionnel de santé créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Professionnel de santé créé avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/HealthcareProfessionalResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(
        #[MapRequestPayload] HealthcareProfessionalRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->professionalService->create($dto);

        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_professionals_get_by_id', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupérer un professionnel de santé par son ID',
        description: 'Permet d’obtenir les détails complets d’un professionnel de santé à partir de son identifiant unique.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Identifiant unique (UUID) du professionnel',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 200,
        description: 'Professionnel de santé trouvé',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'data', ref: '#/components/schemas/HealthcareProfessionalResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Professionnel de santé non trouvé')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getById(string $id): JsonResponse
    {
        $feedback = $this->professionalService->getById($id);

        $status = $feedback->hasError() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
