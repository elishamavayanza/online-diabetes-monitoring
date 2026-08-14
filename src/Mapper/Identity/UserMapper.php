<?php

namespace App\Mapper\Identity;

use App\DTO\Response\Identity\UserResponseDTO;
use App\Entity\Identity\User;

class UserMapper
{
    public function mapEntityToResponse(User $user): UserResponseDTO
    {
        // Utilisation de la méthode statique fromEntity définie dans le DTO
        return UserResponseDTO::fromEntity($user);
    }
}
