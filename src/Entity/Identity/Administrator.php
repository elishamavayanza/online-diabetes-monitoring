<?php

namespace App\Entity\Identity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Représente un administrateur héritant de l'entité User.
 * Ses privilèges persistants (ROLE_ADMIN ou ROLE_ROOT) sont portés par User.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_administrators')]
class Administrator extends User
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoUrl = null; // ou $avatarUrl selon le nommage utilisé

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }
}
