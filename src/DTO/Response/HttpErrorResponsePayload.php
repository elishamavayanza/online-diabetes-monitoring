<?php

namespace App\DTO\Response;

final readonly class HttpErrorResponsePayload
{
    public function __construct(
        public int $status,
        public string $error,
        public string $message,
        public mixed $details = null
    ) {}
}
