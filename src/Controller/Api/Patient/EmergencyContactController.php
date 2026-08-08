<?php

namespace App\Controller\Api\Patient;

use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\Service\Patient\EmergencyContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/emergency-contacts')]
class EmergencyContactController extends AbstractController
{
    public function __construct(
        private readonly EmergencyContactService $contactService
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] EmergencyContactRequestDTO $dto): JsonResponse
    {
        $feedback = $this->contactService->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
