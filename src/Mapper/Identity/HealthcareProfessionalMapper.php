<?php

namespace App\Mapper\Identity;

use App\DTO\Request\Identity\HealthcareProfessionalCreateRequestDTO;
use App\DTO\Request\Identity\HealthcareProfessionalUpdateRequestDTO;
use App\DTO\Response\Identity\HealthcareProfessionalResponseDTO;
use App\Entity\Common\Gender;
use App\Entity\Identity\Address;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\ProfessionalType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HealthcareProfessionalMapper
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function mapCreateRequestToEntity(HealthcareProfessionalCreateRequestDTO $dto): HealthcareProfessional
    {
        $professional = new HealthcareProfessional();

        $professional->setEmail($dto->email);
        $professional->setPhone($dto->phone);
        $professional->setFullName($dto->fullName);
        $professional->setAvatarUrl($dto->avatarUrl);

        // Conversion de la string en Enum Gender
        $professional->setGender(Gender::from($dto->gender));

        $professional->setLocale($dto->locale);

        $professional->setLicenseNumber($dto->licenseNumber);

        // Conversion de la string en Enum ProfessionalType
        $professional->setProfessionalType(ProfessionalType::from($dto->professionalType));

        $professional->setSpecialty($dto->specialty);
        $professional->setSignatureUrl($dto->signatureUrl);

        if (!empty($dto->password)) {
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $dto->password);
            $professional->setPasswordHash($hashedPassword);
        }

        $address = new Address();
        $address->setStreet($dto->street);
        $address->setCity($dto->city);
        $address->setPostalCode($dto->postalCode);
        $address->setCountry($dto->country);
        $professional->setAddress($address);

        return $professional;
    }

    public function mapUpdateRequestToEntity(
        HealthcareProfessionalUpdateRequestDTO $dto,
        HealthcareProfessional $professional
    ): HealthcareProfessional {
        if ($dto->email !== null) {
            $professional->setEmail($dto->email);
        }
        if ($dto->phone !== null) {
            $professional->setPhone($dto->phone);
        }
        if ($dto->fullName !== null) {
            $professional->setFullName($dto->fullName);
        }
        if ($dto->avatarUrl !== null) {
            $professional->setAvatarUrl($dto->avatarUrl);
        }
        if ($dto->gender !== null) {
            $professional->setGender(Gender::from($dto->gender));
        }
        if ($dto->locale !== null) {
            $professional->setLocale($dto->locale);
        }

        if ($dto->licenseNumber !== null) {
            $professional->setLicenseNumber($dto->licenseNumber);
        }
        if ($dto->professionalType !== null) {
            $professional->setProfessionalType(ProfessionalType::from($dto->professionalType));
        }
        if ($dto->specialty !== null) {
            $professional->setSpecialty($dto->specialty);
        }
        if ($dto->signatureUrl !== null) {
            $professional->setSignatureUrl($dto->signatureUrl);
        }

        if (!empty($dto->password)) {
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $dto->password);
            $professional->setPasswordHash($hashedPassword);
        }

        // Préserver l'adresse existante ou en créer une si absente
        if (
            $dto->street !== null ||
            $dto->city !== null ||
            $dto->postalCode !== null ||
            $dto->country !== null
        ) {
            $address = $professional->getAddress();
            if ($address === null) {
                $address = new Address();
                $professional->setAddress($address);
            }

            if ($dto->street !== null) {
                $address->setStreet($dto->street);
            }
            if ($dto->city !== null) {
                $address->setCity($dto->city);
            }
            if ($dto->postalCode !== null) {
                $address->setPostalCode($dto->postalCode);
            }
            if ($dto->country !== null) {
                $address->setCountry($dto->country);
            }
        }

        return $professional;
    }

    public function mapEntityToResponse(HealthcareProfessional $professional): HealthcareProfessionalResponseDTO
    {
        return HealthcareProfessionalResponseDTO::fromEntity($professional);
    }
}
