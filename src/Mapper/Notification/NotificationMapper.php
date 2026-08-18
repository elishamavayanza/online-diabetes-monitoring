<?php

namespace App\Mapper\Notification;

use App\DTO\Request\Notification\NotificationRequestDTO;
use App\DTO\Response\Notification\NotificationResponseDTO;
use App\Entity\Appointment\ReminderChannel;
use App\Entity\Notification\Notification;
use App\Entity\Notification\NotificationType;
use App\Entity\Notification\NotificationChannel;
use App\Entity\Identity\User;

class NotificationMapper
{
    public function mapRequestToEntity(
        NotificationRequestDTO $dto,
        User $user,
        ?Notification $notification = null
    ): Notification {
        $notification ??= new Notification();

        $notification->setUser($user);

        if ($dto->type !== null) {
            $notification->setType(is_string($dto->type) ? NotificationType::tryFrom($dto->type) : $dto->type);
        }

        $notification->setTitle($dto->title);
        $notification->setBody($dto->body);

        if ($dto->channel !== null) {
            $notification->setChannel(is_string($dto->channel) ? ReminderChannel::tryFrom($dto->channel) : $dto->channel);
        }

        $notification->setReadAt($dto->readAt);
        $notification->setRelatedEntityType($dto->relatedEntityType);
        $notification->setRelatedEntityId($dto->relatedEntityId);

        return $notification;
    }

    public function mapEntityToResponse(Notification $notification): NotificationResponseDTO
    {
        return NotificationResponseDTO::fromEntity($notification);
    }
}
