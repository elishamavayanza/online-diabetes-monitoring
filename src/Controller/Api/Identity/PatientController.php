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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/patients')]
#[OA\Tag(
    name: 'Identity - Patients',
    description: 'Gestion des profils patients'
)]
class PatientController extends AbstractController
{
    public function __construct(
        private readonly PatientService $patientService,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route(
        '/{id}/profile',
        name: 'api_patients_get_profile',
        methods: ['GET']
    )]
    #[OA\Get(
        summary: 'Récupérer le profil complet du patient',
        description: 'Permet de récupérer toutes les informations du profil patient ainsi que ses données associées.'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID ou ID du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Profil patient récupéré avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Profil patient récupéré avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PatientResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Permission insuffisante')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getProfile(int $id): JsonResponse
    {
        $feedback = $this->patientService->getProfile($id);

        $status = $feedback->hasErrors()
            ? Response::HTTP_NOT_FOUND
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route(
        '/{id}/profile',
        name: 'api_patients_update_profile',
        methods: ['PUT', 'PATCH']
    )]
    #[OA\Put(
        description: 'Complète ou met à jour le profil métier d’un patient existant (PUT).',
        summary: 'Compléter / Mettre à jour le profil patient (PUT)'
    )]
    #[OA\Patch(
        description: 'Met à jour partiellement le profil métier d’un patient existant (PATCH).',
        summary: 'Mettre à jour partiellement le profil patient (PATCH)'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'UUID du compte utilisateur',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    // Le RequestBody est placé ici en dehors de OA\Put/OA\Patch pour s'appliquer aux deux méthodes de la route
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                ref: new Model(type: PatientRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Profil patient mis à jour',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Profil patient mis à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: PatientResponseDTO::class))
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
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // 1. Fusionner les données du formulaire et les fichiers
        $formData = array_merge(
            $request->request->all(),
            $request->files->all()
        );

        // 2. Désérialiser manuellement vers le DTO PatientRequestDTO
        $dto = $this->serializer->denormalize(
            $formData,
            PatientRequestDTO::class,
            null,
            ['allow_extra_attributes' => true]
        );

        // 3. Valider manuellement le DTO
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Données invalides',
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        // 4. Appel du service
        $feedback = $this->patientService->updateProfile($id, $dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

}
