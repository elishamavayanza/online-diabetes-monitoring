<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\PatientRequestDTO;
use App\DTO\Response\Identity\PatientResponseDTO;
use App\Service\Identity\PatientService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients')]
#[OA\Tag(
    name: 'Identity - Patients',
    description: 'Gestion des profils patients'
)]
class PatientController extends AbstractController
{
    public function __construct(
        private readonly PatientService $patientService
    ) {
    }

    #[Route(
        '/{id}/profile',
        name: 'api_patients_update_profile',
        methods: ['PUT', 'PATCH']
    )]
    #[OA\Put(
        summary: 'Compléter le profil patient',
        description: <<<'DESC'
Complète ou met à jour le profil métier d'un patient existant.

Le compte utilisateur doit avoir été créé préalablement
via POST /api/users.

Cette opération ne crée pas de compte et ne modifie pas
les informations d'authentification.
DESC
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID du compte utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: PatientRequestDTO::class
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Profil patient mis à jour',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    type: 'integer',
                    example: 200
                ),
                new OA\Property(
                    property: 'error',
                    type: 'boolean',
                    example: false
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Profil patient mis à jour avec succès.'
                ),
                new OA\Property(
                    property: 'data',
                    ref: new Model(
                        type: PatientResponseDTO::class
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    #[OA\Response(
        response: 403,
        description: 'Permission insuffisante'
    )]
    #[OA\Response(
        response: 404,
        description: 'Utilisateur ou profil patient introuvable'
    )]
    public function updateProfile(
        int $id,
        #[MapRequestPayload]
        PatientRequestDTO $dto
    ): JsonResponse {
        $feedback = $this->patientService->updateProfile(
            $id,
            $dto
        );

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json(
            $feedback,
            $status
        );
    }
}
