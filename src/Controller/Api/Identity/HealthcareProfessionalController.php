<?php

namespace App\Controller\Api\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalCreateRequestDTO;
use App\DTO\Request\Identity\HealthcareProfessionalUpdateRequestDTO;
use App\DTO\Response\Identity\HealthcareProfessionalResponseDTO;
use App\Service\Identity\HealthcareProfessionalService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/professionals')]
#[OA\Tag(
    name: 'Identity - Professionals',
    description: 'Gestion des professionnels de santé'
)]
class HealthcareProfessionalController extends AbstractController
{
    public function __construct(
        private readonly HealthcareProfessionalService $professionalService,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'api_professionals_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste des professionnels de santé actifs.',
        summary: 'Lister les professionnels de santé'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    #[OA\Response(
        response: 403,
        description: 'Permission insuffisante'
    )]
    public function list(): JsonResponse
    {
        $feedback = $this->professionalService->getAll();

        return $this->json(
            $feedback,
            Response::HTTP_OK
        );
    }

    #[Route('', name: 'api_professionals_create', methods: ['POST'])]
    #[OA\Post(
        description: <<<'DESC'
Crée un compte professionnel de santé.

Le rôle de sécurité est attribué automatiquement selon le professionalType :
- CLINICIAN → ROLE_CLINICIAN
- NUTRITIONIST → ROLE_NUTRITIONIST

Le client ne peut pas fournir directement un rôle de sécurité.
DESC,
        summary: 'Créer un professionnel de santé'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                ref: new Model(type: HealthcareProfessionalCreateRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Professionnel créé avec succès',
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
                    example: 'Professionnel créé avec succès.'
                ),
                new OA\Property(
                    property: 'data',
                    ref: new Model(
                        type: HealthcareProfessionalResponseDTO::class
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
    public function create(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $formData = array_merge(
            $request->request->all(),
            $request->files->all()
        );

        /** @var HealthcareProfessionalCreateRequestDTO $dto */
        $dto = $this->serializer->denormalize(
            $formData,
            HealthcareProfessionalCreateRequestDTO::class,
            null,
            ['allow_extra_attributes' => true]
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Données invalides',
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->professionalService->create($dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route(
        '/{id}',
        name: 'api_professionals_get_by_id',
        methods: ['GET']
    )]
    #[OA\Get(
        description: 'Récupère un professionnel par son ID.',
        summary: 'Récupérer un professionnel'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du professionnel',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Professionnel trouvé',
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
                    property: 'data',
                    ref: new Model(
                        type: HealthcareProfessionalResponseDTO::class
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Professionnel introuvable'
    )]
    #[OA\Response(
        response: 401,
        description: 'Non authentifié'
    )]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->professionalService->getById($id);

        $status = $feedback->hasErrors()
            ? Response::HTTP_NOT_FOUND
            : Response::HTTP_OK;

        return $this->json(
            $feedback,
            $status
        );
    }

    #[Route(
        '/{id}',
        name: 'api_professionals_update',
        methods: ['POST', 'PUT', 'PATCH']
    )]
    #[OA\Put(
        summary: 'Modifier un professionnel'
    )]
    #[OA\Patch(
        summary: 'Modifier partiellement un professionnel'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du professionnel',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                ref: new Model(type: HealthcareProfessionalUpdateRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Professionnel mis à jour'
    )]
    #[OA\Response(
        response: 400,
        description: 'Données invalides'
    )]
    #[OA\Response(
        response: 404,
        description: 'Professionnel introuvable'
    )]
    #[OA\Response(
        response: 403,
        description: 'Permission insuffisante'
    )]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $formData = array_merge(
            $request->request->all(),
            $request->files->all()
        );

        /** @var HealthcareProfessionalUpdateRequestDTO $dto */
        $dto = $this->serializer->denormalize(
            $formData,
            HealthcareProfessionalUpdateRequestDTO::class,
            null,
            ['allow_extra_attributes' => true]
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 400,
                'error' => true,
                'message' => 'Données invalides',
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->professionalService->update($id, $dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route(
        '/{id}',
        name: 'api_professionals_delete',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        summary: 'Supprimer un professionnel'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID du professionnel',
        in: 'path',
        required: true,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Professionnel supprimé'
    )]
    #[OA\Response(
        response: 404,
        description: 'Professionnel introuvable'
    )]
    #[OA\Response(
        response: 403,
        description: 'Permission insuffisante'
    )]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->professionalService->delete($id);

        $status = $feedback->hasErrors()
            ? Response::HTTP_NOT_FOUND
            : Response::HTTP_OK;

        return $this->json(
            $feedback,
            $status
        );
    }
}
