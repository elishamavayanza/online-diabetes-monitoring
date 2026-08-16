<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionVersionRequestDTO;
use App\DTO\Response\Treatment\PrescriptionVersionResponseDTO;
use App\Service\Treatment\PrescriptionVersionService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/prescription-versions')]
#[OA\Tag(name: 'Treatment - Prescription Versions', description: 'Gestion de l’historique et des versions de prescriptions')]
class PrescriptionVersionController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionVersionService $service
    ) {}

    #[Route('', name: 'api_prescription_versions_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’archiver un instantané ou une version modifiée d’une prescription.',
        summary: 'Créer une version de prescription'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la version',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionVersionRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionVersionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionVersionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescription_versions_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’une version spécifique de prescription.',
        summary: 'Afficher une version de prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Version récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionVersionResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Version introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getOne(int $id): JsonResponse
    {
        $feedback = $this->service->getOne($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/prescription/{prescriptionId}', name: 'api_prescription_versions_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer l’historique complet des versions d’une prescription.',
        summary: 'Lister les versions d’une prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Historique récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: PrescriptionVersionResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Prescription introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function list(int $prescriptionId): JsonResponse
    {
        $feedback = $this->service->getAllByPrescription($prescriptionId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescription_versions_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer une version de l’historique (si nécessaire).',
        summary: 'Supprimer une version de prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Version supprimée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Version supprimée avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Version introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
