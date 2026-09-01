<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Service\Patient\MedicalConsentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    #[OA\Post(
        description: 'Enregistrer un consentement médical avec fichier',
        summary: 'Créer un consentement',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: new Model(type: MedicalConsentRequestDTO::class))
            )
        )
    )]
    public function create(Request $request): JsonResponse
    {
        $revokedAtStr = $request->request->get('revokedAt');
        $revokedAt = (!empty($revokedAtStr) && $revokedAtStr !== 'null') ? new \DateTimeImmutable($revokedAtStr) : null;

        $grantedAtStr = $request->request->get('grantedAt');
        $grantedAt = (!empty($grantedAtStr) && $grantedAtStr !== 'null') ? new \DateTimeImmutable($grantedAtStr) : new \DateTimeImmutable();

        $dto = new MedicalConsentRequestDTO(
            patientId: $request->request->get('patientId'),
            organizationId: $request->request->get('organizationId'),
            consentType: $request->request->get('consentType'),
            grantedAt: $grantedAt,
            revokedAt: $revokedAt,
            documentFile: $request->files->get('documentFile')
        );

        $feedback = $this->consentService->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_medical_consents_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Modifier un consentement médical existant (multipart/form-data)',
        summary: 'Mettre à jour un consentement',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: new Model(type: MedicalConsentRequestDTO::class))
            )
        )
    )]
    public function update(string $id, Request $request): JsonResponse
    {
        $revokedAtStr = $request->request->get('revokedAt');
        $revokedAt = (!empty($revokedAtStr) && $revokedAtStr !== 'null') ? new \DateTimeImmutable($revokedAtStr) : null;

        $grantedAtStr = $request->request->get('grantedAt');
        $grantedAt = (!empty($grantedAtStr) && $grantedAtStr !== 'null') ? new \DateTimeImmutable($grantedAtStr) : new \DateTimeImmutable();

        $dto = new MedicalConsentRequestDTO(
            patientId: $request->request->get('patientId'),
            organizationId: $request->request->get('organizationId'),
            consentType: $request->request->get('consentType'),
            grantedAt: $grantedAt,
            revokedAt: $revokedAt,
            documentFile: $request->files->get('documentFile')
        );

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
