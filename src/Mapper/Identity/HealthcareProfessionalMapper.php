<?php

namespace App\Mapper\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\DTO\Response\Identity\HealthcareProfessionalResponseDTO;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HealthcareProfessionalMapper
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function mapRequestToEntity(HealthcareProfessionalRequestDTO $dto, ?HealthcareProfessional $professional = null): HealthcareProfessional
    {
        $professional ??= new HealthcareProfessional();

        $professional->setEmail($dto->email);
        $professional->setPhone($dto->phone);
        $professional->setFullName($dto->fullName);
        $professional->setAvatarUrl($dto->avatarUrl);
        $professional->setGender($dto->gender);
        $professional->setLocale($dto->locale);
        $professional->setLicenseNumber($dto->licenseNumber);
        $professional->setProfessionalType($dto->professionalType);
        $professional->setSpecialty($dto->specialty);
        $professional->setSignatureUrl($dto->signatureUrl);

        if (!empty($dto->password)) {
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $dto->password);
            $professional->setPassword($hashedPassword);
        }

        // Gestion de l'adresse embeddable ou liée
        $address = new Address();
        $address->setStreet($dto->street);
        $address->setCity($dto->city);
        $address->setPostalCode($dto->postalCode);
        $address->setCountry($dto->country);

        $professional->setAddress($address);

        return $professional;
    }

    public function mapEntityToResponse(HealthcareProfessional $professional): HealthcareProfessionalResponseDTO
    {
        return HealthcareProfessionalResponseDTO::fromEntity($professional);
    }
}
