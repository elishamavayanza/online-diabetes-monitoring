<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\DTO\Response\Medical\LaboratoryResultResponseDTO;
use App\Service\Medical\LaboratoryResultService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/patients/{patientId}/laboratory-results')]
#[OA\Tag(name: 'Medical - Laboratory Results', description: 'Gestion des résultats de laboratoire')]
class LaboratoryResultController extends AbstractController
{
    public function __construct(
        private readonly LaboratoryResultService $service
    ) {}

    #[Route('', name: 'api_laboratory_results_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste des résultats de laboratoire d’un patient.',
        summary: 'Lister les résultats de laboratoire d’un patient'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function list(string $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_laboratory_results_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer un nouveau résultat d’examen de laboratoire avec un fichier pour un patient.',
        summary: 'Ajouter un résultat de laboratoire'
    )]
    #[OA\Parameter(
        name: 'patientId',
        description: 'Identifiant unique (UUID) du patient',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: 'Paramètres et fichier du résultat de laboratoire',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                ref: new Model(type: LaboratoryResultRequestDTO::class)
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Résultat de laboratoire ajouté avec succès'
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(
        string $patientId,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $dto = new LaboratoryResultRequestDTO(
            testName: $request->request->get('testName'),
            file: $request->files->get('file'),
            labName: $request->request->get('labName')
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{resultId}', name: 'api_laboratory_results_update', methods: ['POST', 'PUT', 'PATCH'])]
    #[OA\Post(
        description: 'Permet de modifier un résultat de laboratoire existant (utiliser POST avec _method=PUT ou PUT/PATCH selon votre client HTTP pour le multipart).',
        summary: 'Modifier un résultat de laboratoire'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'resultId', description: 'Identifiant du résultat', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                ref: new Model(type: LaboratoryResultRequestDTO::class)
            )
        )
    )]
    #[OA\Response(response: 200, description: 'Résultat mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Patient ou résultat non trouvé')]
    public function update(
        string $patientId,
        string $resultId,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $dto = new LaboratoryResultRequestDTO(
            testName: $request->request->get('testName'),
            file: $request->files->get('file'),
            labName: $request->request->get('labName')
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->service->update($patientId, $resultId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{resultId}', name: 'api_laboratory_results_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer un résultat de laboratoire.',
        summary: 'Supprimer un résultat de laboratoire'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'resultId', description: 'Identifiant du résultat', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Résultat supprimé avec succès')]
    #[OA\Response(response: 404, description: 'Patient ou résultat non trouvé')]
    public function delete(string $patientId, string $resultId): JsonResponse
    {
        $feedback = $this->service->delete($patientId, $resultId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
