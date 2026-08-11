<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\DTO\Response\Appointment\AppointmentResponseDTO;
use App\Service\Appointment\AppointmentService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointments')]
#[OA\Tag(
    name: 'Appointments',
    description: 'Gestion professionnelle des rendez-vous médicaux pour les patients et professionnels de santé'
)]
class AppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentService $service
    ) {}

    #[Route('', name: 'api_appointments_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de planifier un rendez-vous entre un patient et un professionnel de santé dans une organisation ou un établissement de santé spécifique, avec vérification des permissions et des règles métier.',
        summary: 'Créer un nouveau rendez-vous médical'
    )]
    #[OA\RequestBody(
        description: 'Données nécessaires à la création du rendez-vous',
        required: true,
        content: new OA\JsonContent(
            required: [
                'patientId',
                'professionalId',
                'organizationId',
                'scheduledAt',
                'durationMinutes',
                'status'
            ],
            properties: [
                new OA\Property(
                    property: 'patientId',
                    description: 'Identifiant unique du patient',
                    type: 'string',
                    format: 'uuid',
                    example: '4a613328-98e3-4d64-8898-0c06a3861c8f'
                ),
                new OA\Property(
                    property: 'professionalId',
                    description: 'Identifiant unique du professionnel de santé',
                    type: 'string',
                    format: 'uuid',
                    example: '7b224119-12f4-4b53-9912-1f83c2748a12'
                ),
                new OA\Property(
                    property: 'organizationId',
                    description: 'Identifiant unique de l’organisation de santé',
                    type: 'string',
                    format: 'uuid',
                    example: '1c552144-88ef-4a92-b4c4-7893a12b4e55'
                ),
                new OA\Property(
                    property: 'facilityId',
                    description: 'Identifiant de l’établissement',
                    type: 'string',
                    format: 'uuid',
                    example: '9f881245-33ee-4b11-9a21-4f88e1478c99',
                    nullable: true
                ),
                new OA\Property(
                    property: 'scheduledAt',
                    description: 'Date et heure prévues du rendez-vous',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-08-15T10:30:00Z'
                ),
                new OA\Property(
                    property: 'durationMinutes',
                    description: 'Durée du rendez-vous en minutes',
                    type: 'integer',
                    example: 30
                ),
                new OA\Property(
                    property: 'status',
                    description: 'Statut initial du rendez-vous',
                    type: 'string',
                    example: 'SCHEDULED'
                ),
                new OA\Property(
                    property: 'reason',
                    description: 'Motif du rendez-vous',
                    type: 'string',
                    example: 'Contrôle trimestriel du taux de glycémie',
                    nullable: true,
                    maxLength: 255
                ),
                new OA\Property(
                    property: 'notes',
                    description: 'Notes cliniques ou administratives additionnelles',
                    type: 'string',
                    example: 'Le patient apporte ses derniers résultats d’analyse sanguine.',
                    nullable: true,
                    maxLength: 5000
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Rendez-vous créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    type: 'integer',
                    example: 201
                ),
                new OA\Property(
                    property: 'error',
                    type: 'boolean',
                    example: false
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Rendez-vous créé avec succès.'
                ),
                new OA\Property(
                    property: 'data',
                    ref: new Model(type: AppointmentResponseDTO::class)
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Erreur de validation ou données invalides',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'status' => 400,
                'error' => true,
                'message' => 'Patient introuvable.',
                'data' => null
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié - Jeton JWT manquant ou invalide'
    )]
    #[OA\Response(
        response: 403,
        description: 'Accès refusé - Permissions insuffisantes pour cette organisation'
    )]
    public function create(
        #[MapRequestPayload] AppointmentRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->service->create($dto);

        $status = $feedback->hasError()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
