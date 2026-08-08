<?php

namespace App\Mapper\Notification;

use App\DTO\Request\Notification\ReminderRuleRequestDTO;
use App\DTO\Response\Notification\ReminderRuleResponseDTO;
use App\Entity\Notification\ReminderRule;
use App\Entity\Notification\ReminderTargetType;
use App\Entity\Identity\Patient;

class ReminderRuleMapper
{
    public function mapRequestToEntity(
        ReminderRuleRequestDTO $dto,
        Patient $patient,
        ?ReminderRule $rule = null
    ): ReminderRule {
        $rule ??= new ReminderRule();

        $rule->setPatient($patient);

        if ($dto->targetType !== null) {
            $rule->setTargetType(is_string($dto->targetType) ? ReminderTargetType::tryFrom($dto->targetType) : $dto->targetType);
        }

        $rule->setRelatedEntityId($dto->relatedEntityId);
        $rule->setCronExpression($dto->cronExpression);
        $rule->setActive($dto->active);

        return $rule;
    }

    public function mapEntityToResponse(ReminderRule $rule): ReminderRuleResponseDTO
    {
        return ReminderRuleResponseDTO::fromEntity($rule);
    }
}
