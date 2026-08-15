<?php

namespace App\Entity\Identity;

use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\OrganizationMembership;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Classe abstraite de base pour les utilisateurs du système (héritant de Person),
 * implémentant les interfaces de sécurité Symfony.
 */
#[ORM\Entity]
#[ORM\Table(name: 'identity_users')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'user_type', type: 'string')]
#[ORM\DiscriminatorMap([
    'patient' => Patient::class,
    'professional' => HealthcareProfessional::class,
    'administrator' => Administrator::class
])]
abstract class User extends Person implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var string|null L'adresse e-mail de l'utilisateur (unique, sert d'identifiant de connexion).
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    protected ?string $email = null;

    /**
     * @var string|null Le hachage du mot de passe de l'utilisateur.
     */
    #[ORM\Column(type: 'string')]
    protected ?string $passwordHash = null;

    /**
     * @var string|null La langue ou la locale préférée de l'utilisateur.
     */
    #[ORM\Column(type: 'string', length: 10)]
    protected ?string $locale = 'fr';

    /**
     * @var UserStatus|null Le statut du compte utilisateur.
     */
    #[ORM\Column(type: 'string', length: 50, enumType: UserStatus::class)]
    protected ?UserStatus $status = UserStatus::PENDING_ACTIVATION;

    /**
     * @var DateTimeImmutable|null La date et l'heure de vérification de l'e-mail.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $emailVerifiedAt = null;

    /**
     * @var DateTimeImmutable|null La date et l'heure de la dernière connexion.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $lastLoginAt = null;

    /** @var list<string> */
    #[ORM\Column(type: Types::JSON)]
    protected array $roles = [];

    /** @var Collection<int, OrganizationMembership> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OrganizationMembership::class)]
    protected Collection $organizationMemberships;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $resetToken = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $resetTokenExpiresAt = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    protected int $loginAttempts = 0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $lockedUntil = null;

    public function __construct()
    {
        $this->organizationMemberships = new ArrayCollection();
    }

    /**
     * Récupère l'e-mail.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'e-mail.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur pour l'authentification (requis par UserInterface).
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Récupère le mot de passe (requis par PasswordAuthenticatedUserInterface).
     */
    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Définit le mot de passe.
     */
    public function setPassword(string $password): static
    {
        $this->passwordHash = $password;
        return $this;
    }

    /**
     * Récupère le hachage du mot de passe.
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Définit le hachage du mot de passe.
     */
    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * Récupère la locale.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Définit la locale.
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Récupère le statut de l'utilisateur.
     */
    public function getStatus(): ?UserStatus
    {
        return $this->status;
    }

    /**
     * Définit le statut de l'utilisateur.
     */
    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Récupère la date de vérification de l'e-mail.
     */
    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    /**
     * Définit la date de vérification de l'e-mail.
     */
    public function setEmailVerifiedAt(?DateTimeImmutable $emailVerifiedAt): static
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    /**
     * Récupère la date de dernière connexion.
     */
    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    /**
     * Définit la date de dernière connexion.
     */
    public function setLastLoginAt(?DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;
        return $this;
    }

    /**
     * Retourne les rôles de sécurité attribués à l'utilisateur.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        return array_values(array_unique($this->roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return Collection<int, OrganizationMembership>
     */
    public function getOrganizationMemberships(): Collection
    {
        return $this->organizationMemberships;
    }

    public function addOrganizationMembership(OrganizationMembership $membership): static
    {
        if (!$this->organizationMemberships->contains($membership)) {
            $this->organizationMemberships->add($membership);
            $membership->setUser($this);
        }

        return $this;
    }

    /**
     * Efface les données sensibles temporaires de l'utilisateur (requis par UserInterface).
     */
    public function eraseCredentials(): void
    {
    }

    public function getResetToken(): ?string { return $this->resetToken; }
    public function setResetToken(?string $resetToken): static { $this->resetToken = $resetToken; return $this; }

    public function getResetTokenExpiresAt(): ?DateTimeImmutable { return $this->resetTokenExpiresAt; }
    public function setResetTokenExpiresAt(?DateTimeImmutable $expiresAt): static { $this->resetTokenExpiresAt = $expiresAt; return $this; }

    public function getLoginAttempts(): int { return $this->loginAttempts; }
    public function setLoginAttempts(int $attempts): static { $this->loginAttempts = $attempts; return $this; }

    public function getLockedUntil(): ?DateTimeImmutable { return $this->lockedUntil; }
    public function setLockedUntil(?DateTimeImmutable $lockedUntil): static { $this->lockedUntil = $lockedUntil; return $this; }
}
