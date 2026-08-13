<?php

namespace App\Mapper\Healthcare;

use App\DTO\Request\Healthcare\OrganizationAdministratorRequestDTO;
use App\DTO\Response\Identity\UserResponseDTO;
use App\Entity\Identity\Address;
use App\Entity\Identity\Administrator;
use App\Entity\Identity\Role;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrganizationAdministratorMapper
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function mapRequestToEntity(OrganizationAdministratorRequestDTO $dto): Administrator
    {
        $administrator = new Administrator();
        $administrator->setEmail($dto->email);
        $administrator->setPassword($this->passwordHasher->hashPassword($administrator, $dto->password));
        $administrator->setFullName($dto->fullName);
        $administrator->setGender($dto->gender);
        $administrator->setPhone($dto->phone);
        $administrator->setLocale($dto->locale);
        $administrator->setAvatarUrl($dto->avatarUrl);
        $administrator->setRoles([Role::ROLE_ADMIN->value]);

        $address = new Address();
        $address->setStreet($dto->street);
        $address->setCity($dto->city);
        $address->setPostalCode($dto->postalCode);
        $address->setCountry($dto->country);
        $administrator->setAddress($address);

        return $administrator;
    }

    public function mapEntityToResponse(Administrator $administrator): UserResponseDTO
    {
        return UserResponseDTO::fromEntity($administrator);
    }
}
