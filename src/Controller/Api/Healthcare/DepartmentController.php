<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\DepartmentRequestDTO;
use App\Service\Healthcare\DepartmentService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/departments')]
#[OA\Tag(name: 'Healthcare - Departments', description: 'Gestion des départements et services médicaux')]
class DepartmentController extends AbstractController
{
    public function __construct(
        private readonly DepartmentService $service
    ) {}

    #[Route('', name: 'api_departments_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un département médical',
        description: 'Permet d’enregistrer un nouveau service ou département au sein d’un établissement de santé.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du département',
        content: new OA\JsonContent(
            required: ['facilityId', 'name'],
            properties: [
                new OA\Property(property: 'facilityId', type: 'string', format: 'uuid', example: '11bb22cc-33ee-4ff1-8811-9a8877665544', description: 'Identifiant unique de l’établissement de santé rattaché'),
                new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Cardiologie', description: 'Nom du département'),
                new OA\Property(property: 'specialty', type: 'string', maxLength: 150, nullable: true, example: 'Cardiologie interventionnelle', description: 'Spécialité médicale associée (optionnel)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Département créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Département créé avec succès.'),
                new OA\Property(property: 'data', ref: '#/components/schemas/DepartmentResponseDTO')
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
    public function create(#[MapRequestPayload] DepartmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
