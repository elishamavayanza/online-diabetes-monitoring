<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\DTO\Response\Treatment\PrescriptionItemResponseDTO;
use App\Service\Treatment\PrescriptionItemService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/prescription-items')]
#[OA\Tag(name: 'Treatment - Prescription Items', description: 'Gestion des lignes d’éléments de prescription')]
class PrescriptionItemController extends AbstractController
{
    public function __construct(
        private readonly PrescriptionItemService $service
    ) {}

    #[Route('', name: 'api_prescription_items_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’associer un médicament et sa posologie à une ordonnance existante.',
        summary: 'Ajouter un élément à une prescription'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’élément de prescription',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionItemRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Élément de prescription créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément ajouté avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescription_items_get', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer les détails d’un élément de prescription par son ID.',
        summary: 'Afficher un élément de prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément de prescription récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Élément introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function getOne(string $id): JsonResponse
    {
        $feedback = $this->service->getOne($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/prescription/{prescriptionId}', name: 'api_prescription_items_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de lister tous les éléments associés à une prescription spécifique.',
        summary: 'Lister les éléments d’une prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des éléments récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Liste récupérée avec succès.'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: PrescriptionItemResponseDTO::class))
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Prescription introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function list(string $prescriptionId): JsonResponse
    {
        $feedback = $this->service->getAllByPrescription($prescriptionId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescription_items_update', methods: ['PUT'])]
    #[OA\Put(
        description: 'Permet de mettre à jour un élément d’une prescription existante.',
        summary: 'Mettre à jour un élément de prescription'
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de l’élément de prescription',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: PrescriptionItemRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément mis à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PrescriptionItemResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Élément introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(string $id, #[MapRequestPayload] PrescriptionItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_prescription_items_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un élément d’une prescription.',
        summary: 'Supprimer un élément de prescription'
    )]
    #[OA\Response(
        response: 200,
        description: 'Élément supprimé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Élément supprimé avec succès.')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Élément introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
