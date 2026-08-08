<?php

namespace App\EventListener;

use App\Dto\Response\HttpErrorResponsePayload;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ConstraintViolationListException;
use Throwable;

#[AsEventListener(event: 'kernel.exception', priority: 0)]
class HttpExceptionListener
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly LoggerInterface $logger
    ) {}

    private function isDevEnvironment(): bool
    {
        $env = $this->params->get('kernel.environment');
        return in_array($env, ['dev', 'development', 'test'], true);
    }

    private function logError(Throwable $e): void
    {
        if (!$this->isDevEnvironment()) {
            return;
        }
        $this->logger->error($e->getMessage());
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ConstraintViolationListException) {
            return;
        }

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = "Une erreur interne du serveur s'est produite. Veuillez réessayer plus tard.";

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof AccessDeniedException) {
            $statusCode = Response::HTTP_FORBIDDEN;
            $message = "Vous n'avez pas l'autorisation d'accéder à cette ressource.";
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $message = "Échec de l'authentification : " . $exception->getMessage();
        }

        $this->logError($exception);

        $payload = new HttpErrorResponsePayload(
            status: $statusCode,
            error: Response::$statusTexts[$statusCode] ?? 'Error',
            message: $message,
            details: $this->isDevEnvironment() ? $exception->getMessage() : null
        );

        $response = new JsonResponse($payload, $statusCode);
        $event->setResponse($response);
    }
}
