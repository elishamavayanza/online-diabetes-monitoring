<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\PrescriptionItemRequestDTO;
use App\Service\Treatment\PrescriptionItemService;
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
        summary: 'Ajouter un élément à une prescription',
        description: 'Permet d’associer un médicament et sa posologie à une ordonnance existante.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de l’élément de prescription',
        content: new OA\JsonContent(
            required: ['prescriptionId', 'medicationId', 'dosage', 'quantity', 'morning', 'noon', 'evening'],
            properties: [
                new OA\Property(property: 'prescriptionId', type: 'string', format: 'uuid', example: '99001122-3344-5566-7788-99aabbccddeev', description: 'ID de la prescription'),
                new OA\Property(property: 'medicationId', type: 'string', format: 'uuid', example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID du médicament'),
                new OA\Property(property: 'dosage', type: 'string', maxLength: 100, example: '1 comprimé', description: 'Posologie'),
                new OA\Property(property: 'quantity', type: 'string', example: '1.00', description: 'Quantité totale prescrite'),
                new OA\Property(property: 'morning', type: 'boolean', example: true, description: 'Prise le matin'),
                new OA\Property(property: 'noon', type: 'boolean', example: false, description: 'Prise le midi'),
                new OA\Property(property: 'evening', type: 'boolean', example: true, description: 'Prise le soir'),
                new OA\Property(property: 'instructions', type: 'string', maxLength: 5000, nullable: true, example: 'À prendre avec un grand verre d’eau.', description: 'Instructions spécifiques')
            ],
            type: 'object'
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
                new OA\Property(property: 'data', ref: '#/components/schemas/PrescriptionItemResponseDTO')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] PrescriptionItemRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
