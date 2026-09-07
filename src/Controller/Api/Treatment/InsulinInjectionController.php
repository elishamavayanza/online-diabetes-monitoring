<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\InsulinInjectionRequestDTO;
use App\Service\Treatment\InsulinInjectionService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/insulin-injections')]
#[OA\Tag(name: 'Treatment - Insulin Injections', description: 'Gestion des injections d’insuline')]
class InsulinInjectionController extends AbstractController
{
    public function __construct(
        private readonly InsulinInjectionService $service
    ) {}

    #[Route('', name: 'api_insulin_injections_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de toutes les injections d’insuline.',
        summary: 'Lister toutes les injections'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès'
    )]
    public function all(): JsonResponse
    {
        $feedback = $this->service->all();
        return $this->json($feedback, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_insulin_injections_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère une injection d’insuline spécifique par son ID.',
        summary: 'Récupérer une injection par ID'
    )]
    #[OA\Response(
        response: 200,
        description: 'Injection récupérée avec succès'
    )]
    #[OA\Response(response: 404, description: 'Injection introuvable')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/patient/{patientId}', name: 'api_insulin_injections_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère l’historique des injections d’insuline d’un patient.',
        summary: 'Historique des injections d’un patient'
    )]
    #[OA\Response(
        response: 200,
        description: 'Historique récupéré avec succès'
    )]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getByPatient(int $patientId): JsonResponse
    {
        $feedback = $this->service->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_insulin_injections_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de tracer l’injection d’insuline réalisée par un patient.',
        summary: 'Enregistrer une injection d’insuline'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’injection',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: InsulinInjectionRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Injection d’insuline enregistrée avec succès'
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] InsulinInjectionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_insulin_injections_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une injection d’insuline existante.',
        summary: 'Mettre à jour une injection d’insuline'
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de l’injection',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: InsulinInjectionRequestDTO::class)
        )
    )]
    #[OA\Response(response: 200, description: 'Mise à jour réussie')]
    #[OA\Response(response: 400, description: 'Erreur de validation')]
    public function update(int $id, #[MapRequestPayload] InsulinInjectionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_insulin_injections_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet à un clinicien de supprimer une injection d’insuline.',
        summary: 'Supprimer une injection d’insuline (Réservé au clinicien)'
    )]
    #[OA\Response(response: 200, description: 'Suppression réussie')]
    #[OA\Response(response: 403, description: 'Accès refusé (non clinicien)')]
    #[OA\Response(response: 404, description: 'Injection introuvable')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}