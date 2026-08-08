<?php

namespace App\DTO;

use Symfony\Component\Validator\ConstraintViolationInterface;

class Feedback implements \JsonSerializable
{
    public const FLUSH_DESCRIPTION = "_flush_description_";

    private ?string $flush = null;
    private ?string $flushDescription = null;
    private int $status = 200;
    private array $errors = [];
    private array $warnings = [];

    public function getFlush(): ?string
    {
        return $this->flush;
    }

    public function setFlush(?string $flush): self
    {
        $this->flush = $flush;
        return $this;
    }

    public function getFlushDescription(): ?string
    {
        return $this->flushDescription;
    }

    public function setFlushDescription(?string $flushDescription): self
    {
        $this->flushDescription = $flushDescription;
        return $this;
    }

    public function setFlushDescriptionWithError(string $flushDescription, bool $error = true): self
    {
        $this->setFlushDescription($flushDescription);
        if ($error) {
            $this->addError(self::FLUSH_DESCRIPTION, $flushDescription);
        } else {
            $this->addWarning(self::FLUSH_DESCRIPTION, $flushDescription);
        }
        return $this;
    }

    public function setErrorFlushDescription(string $flushDescription): self
    {
        return $this->setFlushDescriptionWithError($flushDescription, true);
    }

    public function setWarningFlushDescription(string $flushDescription): self
    {
        return $this->setFlushDescriptionWithError($flushDescription, false);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    public function addWarning(string $field, string $message): self
    {
        $this->warnings[$field] = $message;
        return $this;
    }

    public function bind(iterable $violations): self
    {
        foreach ($violations as $violation) {
            if ($violation instanceof ConstraintViolationInterface) {
                $field = $violation->getPropertyPath();
                if (!isset($this->errors[$field])) {
                    $this->errors[$field] = $violation->getMessage();
                }
            }
        }
        return $this;
    }

    public function autoInitFlush(): self
    {
        $hasErrors = $this->hasErrors();
        $this->setFlush($hasErrors ? "Échec d'exécution de l'opération" : "Succès d'exécution de l'opération");
        $this->setStatus($this->isOk() ? 200 : 422);
        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function isOk(): bool
    {
        return empty($this->errors);
    }

    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    public function jsonSerialize(): array
    {
        return [
            'flush' => $this->flush,
            'flushDescription' => $this->flushDescription,
            'status' => $this->status,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }
}
