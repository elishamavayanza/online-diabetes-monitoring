<?php

namespace App\Controller\Api\Treatment;

use App\DTO\Request\Treatment\MedicationIntakeRequestDTO;
use App\DTO\Response\Treatment\MedicationIntakeResponseDTO;
use App\Service\Treatment\MedicationIntakeService;
use Nelmio\ApiDocBundle\Attribute\Model;
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

    #[Route('', name: 'api_medication_intakes_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère la liste de toutes les prises de médicaments.',
        summary: 'Lister toutes les prises'
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

    #[Route('/{id}', name: 'api_medication_intakes_get_by_id', methods: ['GET'])]
    #[OA\Get(
        description: 'Récupère une prise de médicament spécifique par son ID.',
        summary: 'Récupérer une prise par ID'
    )]
    #[OA\Response(
        response: 200,
        description: 'Prise récupérée avec succès'
    )]
    #[OA\Response(response: 404, description: 'Prise introuvable')]
    public function getById(int $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_medication_intakes_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet de tracer la prise effective d’un élément de prescription par un patient.',
        summary: 'Enregistrer une prise de médicament'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de la prise',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicationIntakeRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Prise de médicament enregistrée avec succès'
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicationIntakeRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medication_intakes_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de modifier une prise de médicament existante.',
        summary: 'Mettre à jour une prise de médicament'
    )]
    #[OA\RequestBody(
        description: 'Nouveaux paramètres de la prise',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: MedicationIntakeRequestDTO::class)
        )
    )]
    #[OA\Response(response: 200, description: 'Mise à jour réussie')]
    #[OA\Response(response: 400, description: 'Erreur de validation')]
    public function update(int $id, #[MapRequestPayload] MedicationIntakeRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medication_intakes_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet à un clinicien de supprimer une prise de médicament.',
        summary: 'Supprimer une prise de médicament (Réservé au clinicien)'
    )]
    #[OA\Response(response: 200, description: 'Suppression réussie')]
    #[OA\Response(response: 403, description: 'Accès refusé (non clinicien)')]
    #[OA\Response(response: 404, description: 'Prise introuvable')]
    public function delete(int $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
