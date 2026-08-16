<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\Service\Patient\EmergencyContactService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/emergency-contacts')]
#[OA\Tag(name: 'Patient - Emergency Contacts', description: 'Gestion des contacts d’urgence des patients')]
class EmergencyContactController extends AbstractController
{
    public function __construct(
        private readonly EmergencyContactService $contactService
    ) {}

    #[Route('/patient/{patientId}', name: 'api_emergency_contacts_by_patient', methods: ['GET'])]
    #[OA\Get(description: 'Lister les contacts d’urgence d’un patient', summary: 'Lister par patient')]
    public function getByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->contactService->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_emergency_contacts_create', methods: ['POST'])]
    #[OA\Post(description: 'Ajouter un contact d’urgence', summary: 'Créer un contact')]
    public function create(#[MapRequestPayload] EmergencyContactRequestDTO $dto): JsonResponse
    {
        $feedback = $this->contactService->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_emergency_contacts_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(description: 'Modifier un contact d’urgence existant', summary: 'Mettre à jour un contact')]
    public function update(string $id, #[MapRequestPayload] EmergencyContactRequestDTO $dto): JsonResponse
    {
        $feedback = $this->contactService->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }


    #[Route('/{id}', name: 'api_emergency_contacts_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprimer un contact d’urgence', summary: 'Supprimer un contact')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->contactService->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
