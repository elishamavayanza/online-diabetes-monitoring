<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Service\Patient\MedicalConsentService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-consents')]
#[OA\Tag(name: 'Patient - Medical Consents', description: 'Gestion des consentements médicaux des patients')]
class MedicalConsentController extends AbstractController
{
    public function __construct(
        private readonly MedicalConsentService $consentService
    ) {}

    #[Route('', name: 'api_medical_consents_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un consentement médical',
        description: 'Permet d’enregistrer un accord ou consentement de traitement/partage de données pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du consentement',
        content: new OA\JsonContent(
            required: ['patientId', 'consentType', 'grantedAt'],
            properties: [
                new OA\Property(property: 'patientId', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient'),
                new OA\Property(property: 'organizationId', type: 'string', format: 'uuid', nullable: true, example: '44aa5566-7788-9900-aabb-ccddeeff1122', description: 'ID de l’organisation (optionnel)'),
                new OA\Property(property: 'consentType', type: 'string', example: 'DATA_SHARING', description: 'Type de consentement'),
                new OA\Property(property: 'grantedAt', type: 'string', format: 'date-time', example: '2026-08-10T10:00:00Z', description: 'Date d’octroi du consentement'),
                new OA\Property(property: 'revokedAt', type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de révocation (optionnel)'),
                new OA\Property(property: 'documentUrl', type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://example.com/consents/doc.pdf', description: 'Lien vers le document signé')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Consentement médical créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Consentement médical créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: App\DTO\Response\Patient\MedicalConsentResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] MedicalConsentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->consentService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
