<?php

namespace App\EventListener;

use App\Dto\Feedback;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ConstraintViolationListException;

#[AsEventListener(event: 'kernel.exception', priority: 10)]
class ConstraintViolationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ConstraintViolationListException || $exception instanceof UnprocessableEntityHttpException) {
            $feedback = new Feedback();
            $feedback->setErrorFlushDescription("Les données envoyées sont invalides.");

            if ($exception instanceof ConstraintViolationListException) {
                $feedback->bind($exception->getConstraintViolationList());
            }

            $response = new JsonResponse(
                $feedback->autoInitFlush(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $event->setResponse($response);
        }
    }
}
