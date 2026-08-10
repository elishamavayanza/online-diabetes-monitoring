<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'administrators')]
class Administrator extends User
{
    public function getRoles(): array
    {
        return [Role::ROLE_ROOT->value];
    }
}
