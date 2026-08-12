<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\DTO\Response\Patient\EmergencyContactResponseDTO;
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

    #[Route('', name: 'api_emergency_contacts_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un contact d’urgence',
        description: 'Permet d’ajouter une personne à contacter en cas d’urgence pour un patient.'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Paramètres du contact d’urgence',
        content: new OA\JsonContent(
            ref: new Model(type: EmergencyContactRequestDTO::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Contact d’urgence créé avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Contact d’urgence créé avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: EmergencyContactResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(#[MapRequestPayload] EmergencyContactRequestDTO $dto): JsonResponse
    {
        $feedback = $this->contactService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
