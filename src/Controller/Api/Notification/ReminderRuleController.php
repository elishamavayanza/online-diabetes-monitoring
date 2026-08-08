<?php

namespace App\Controller\Api\Notification;

use App\DTO\Request\Notification\ReminderRuleRequestDTO;
use App\Service\Notification\ReminderRuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reminder-rules')]
class ReminderRuleController extends AbstractController
{
    public function __construct(
        private readonly ReminderRuleService $service
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] ReminderRuleRequestDTO $dto): JsonResponse
    {
        $feedback = $this->service->create($dto);
        $status = $feedback->hasError() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }
}
