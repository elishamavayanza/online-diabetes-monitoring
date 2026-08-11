<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un administrateur du système héritant de l'entité User,
 * disposant des privilèges de type root.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_administrators')]
class Administrator extends User
{
    /**
     * Retourne les rôles de sécurité attribués à l'administrateur.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return [Role::ROLE_ROOT->value];
    }
}
