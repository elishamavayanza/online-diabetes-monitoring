<?php

namespace App\Controller\Api\Security;

use App\DTO\Feedback;
use App\DTO\Request\Security\SuspensionRequestDTO;
use App\Service\Security\SuspensionService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users')]
#[OA\Tag(name: 'Security - Suspensions', description: 'Suspension et réactivation de comptes (professionnels et patients)')]
class SuspensionController extends AbstractController
{
    public function __construct(
        private readonly SuspensionService $suspensionService
    ) {}

    #[Route('/{id}/suspend', name: 'api_users_suspend', methods: ['POST'])]
    #[OA\Post(
        description: 'Suspend un professionnel ou un patient de l’organisation courante : motif obligatoire, délai optionnel (durée en jours ou date de fin). Un email est envoyé à la personne suspendue.',
        summary: 'Suspendre un compte (professionnel ou patient)'
    )]
    #[OA\RequestBody(
        description: 'Motif et délai de la suspension',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'reason', type: 'string', example: 'Absences répétées aux rendez-vous'),
                new OA\Property(property: 'durationDays', type: 'integer', example: 15),
                new OA\Property(property: 'startsAt', type: 'string', format: 'date-time', example: '2026-09-10T08:00:00+00:00'),
                new OA\Property(property: 'endsAt', type: 'string', format: 'date-time', example: '2026-10-10T08:00:00+00:00')
            ]
        )
    )]
    public function suspend(string $id, #[MapRequestPayload] SuspensionRequestDTO $dto): JsonResponse
    {
        $feedback = $this->suspensionService->suspendUser($id, $dto);
        $status = $this->statusFor($feedback);

        return $this->json($feedback, $status);
    }

    #[Route('/{id}/reactivate', name: 'api_users_reactivate', methods: ['POST'])]
    #[OA\Post(
        description: 'Lève la suspension d’un professionnel ou d’un patient et lui envoie un email de confirmation.',
        summary: 'Réactiver un compte suspendu'
    )]
    public function reactivate(string $id): JsonResponse
    {
        $feedback = $this->suspensionService->reactivateUser($id);
        $status = $this->statusFor($feedback);

        return $this->json($feedback, $status);
    }

    private function statusFor(Feedback $feedback): int
    {
        return $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;
    }
}