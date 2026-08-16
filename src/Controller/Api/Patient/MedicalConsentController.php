<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Service\Patient\MedicalConsentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-consents')]
#[OA\Tag(name: 'Patient - Medical Consents', description: 'Gestion des consentements médicaux')]
class MedicalConsentController extends AbstractController
{
    public function __construct(
        private readonly MedicalConsentService $consentService
    ) {}

    #[Route('/patient/{patientId}', name: 'api_medical_consents_by_patient', methods: ['GET'])]
    #[OA\Get(description: 'Lister les consentements d’un patient', summary: 'Lister par patient')]
    public function getByPatient(string $patientId): JsonResponse
    {
        $feedback = $this->consentService->getByPatient($patientId);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_medical_consents_create', methods: ['POST'])]
    #[OA\Post(description: 'Enregistrer un consentement médical', summary: 'Créer un consentement')]
    public function create(#[MapRequestPayload] MedicalConsentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->consentService->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_consents_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(description: 'Modifier un consentement médical existant', summary: 'Mettre à jour un consentement')]
    public function update(string $id, #[MapRequestPayload] MedicalConsentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->consentService->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_consents_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprimer un consentement médical', summary: 'Supprimer un consentement')]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->consentService->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }


}
