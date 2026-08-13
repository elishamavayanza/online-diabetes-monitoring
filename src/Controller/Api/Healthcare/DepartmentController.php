<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\DepartmentRequestDTO;
use App\DTO\Response\Healthcare\DepartmentResponseDTO;
use App\Service\Healthcare\DepartmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
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


    #[Route('/facility/{facilityId}', name: 'api_departments_by_facility', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de tous les départements rattachés à un établissement de santé spécifique.',
        summary: 'Lister les départements d’un établissement'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste des départements récupérée avec succès.'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: new Model(type: DepartmentResponseDTO::class)))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Établissement introuvable')]
    public function getByFacility(string $facilityId): JsonResponse
    {
        $feedback = $this->service->getByFacility($facilityId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
    #[Route('', name: 'api_departments_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer un nouveau service ou département au sein d’un établissement de santé.',
        summary: 'Créer un département médical'
    )]
    #[OA\RequestBody(
        description: 'Paramètres du département',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: DepartmentRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: DepartmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] DepartmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_departments_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier les informations d’un département médical existant.',
        summary: 'Modifier un département médical'
    )]
    #[OA\RequestBody(
        description: 'Paramètres mis à jour du département',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: DepartmentRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Département mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Département mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DepartmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Département introuvable')]
    public function update(string $id, #[MapRequestPayload] DepartmentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }


    #[Route('/{id}/suspend', name: 'api_departments_suspend', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Permet de suspendre ou réactiver un département médical.',
        summary: 'Suspendre ou réactiver un département'
    )]
    #[OA\Response(
        response: 200,
        description: 'Statut du département mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Statut du département mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: DepartmentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Département introuvable')]
    public function suspend(string $id): JsonResponse
    {
        $feedback = $this->service->toggleSuspend($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
