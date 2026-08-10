<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\Service\Treatment\MedicationIntakeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medication-intakes')]
#[OA\Tag(name: 'Treatment - Medication Intakes', description: 'Gestion des prises de médicaments')]
class MedicationIntakeController extends AbstractController
{
    public function __construct(
        private readonly MedicationIntakeService $service
    ) {}

    #[Route('', name: 'api_medication_intakes_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Enregistrer une prise de médicament',
        description: 'Permet de tracer la prise effective d’un élément de prescription par un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres de la prise',
        content: new OA\JsonContent(
            required: ['prescriptionItemId', 'takenAt', 'quantityTaken', 'status'],
            properties: [
                new OA\Property(property: 'prescriptionItemId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID de l’élément de prescription'),
                new OA\Property(property: 'takenAt', type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z', description: 'Date et heure de la prise'),
                new OA\Property(property: 'quantityTaken', type: 'string', example: '1.00', description: 'Quantité prise'),
                new OA\Property(property: 'status', type: 'string', example: 'TAKEN', description: 'Statut de la prise (ex: TAKEN, MISSED, SKIPPED)')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Prise de médicament enregistrée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Prise enregistrée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Treatment\MedicationIntakeResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicationIntakeRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
