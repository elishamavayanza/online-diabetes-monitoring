<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Service\Patient\MedicalConsentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medical-consents')]
class MedicalConsentController extends AbstractController
{
    public function __construct(
        private readonly MedicalConsentService $consentService
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] MedicalConsentRequestDTO $dto): JsonResponse
    {
        $feedback = $this->consentService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
