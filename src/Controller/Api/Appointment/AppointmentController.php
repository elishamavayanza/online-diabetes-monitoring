<?php

namespace App\Controller\Api\Appointment;

use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\DTO\Response\Appointment\AppointmentResponseDTO;
use App\Service\Appointment\AppointmentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
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
            ref: new Model(type: AppointmentRequestDTO::class)
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
