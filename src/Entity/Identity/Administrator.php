<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'identity_administrators')]
class Administrator extends User
{
    public function getRoles(): array
    {
        return [Role::ROLE_ROOT->value];
    }
}
