<?php

namespace App\Controller\Api\Medical;

use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\DTO\Response\Medical\LaboratoryResultResponseDTO;
use App\Service\Medical\LaboratoryResultService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

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
        description: 'Permet d’enregistrer un nouveau résultat d’examen de laboratoire pour un patient.',
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
        description: 'Paramètres du résultat de laboratoire',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: LaboratoryResultRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Résultat de laboratoire ajouté avec succès'
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 404, description: 'Patient non trouvé')]
    public function create(string $patientId, #[MapRequestPayload] LaboratoryResultRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($patientId, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }


    #[Route('/{resultId}', name: 'api_laboratory_results_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier un résultat de laboratoire existant.',
        summary: 'Modifier un résultat de laboratoire'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'resultId', description: 'Identifiant du résultat', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: LaboratoryResultRequestDTO::class)))]
    #[OA\Response(response: 200, description: 'Résultat mis à jour avec succès')]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 404, description: 'Patient ou résultat non trouvé')]
    public function update(
        string $patientId,
        string $resultId,
        #[MapRequestPayload] LaboratoryResultRequestDTO $dto
    ): JsonResponse {
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
