<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\DTO\Response\Patient\MedicalConsentResponseDTO;
use App\Service\Patient\MedicalConsentService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
            ref: new Model(type: MedicalConsentRequestDTO::class)
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
                new OA\Property(property: 'data', ref: new Model(type: MedicalConsentResponseDTO::class))
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
