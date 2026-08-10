<?php

namespace App\Service\Security;

use App\Entity\Identity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManager
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @throws \Exception Si l'ancien mot de passe est invalide
     */
    public function updatePassword(User $user, string $oldPassword, string $newPassword): void
    {
        // 1. Vérification de l'ancien mot de passe
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new \InvalidArgumentException('Ancien mot de passe incorrect.');
        }

        // 2. Hashage et mise à jour
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        // 3. Persistance
        $this->entityManager->flush();
    }
}
