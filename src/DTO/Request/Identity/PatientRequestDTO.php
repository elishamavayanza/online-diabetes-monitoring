<?php

namespace App\DTO\Request\Identity;

use App\Entity\Common\Gender;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PatientRequestDTO',
    description: 'Structure des données requises pour la création d’un patient'
)]
class PatientRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Assert\Length(max: 180)]
        #[OA\Property(type: 'string', format: 'email', maxLength: 180, example: 'patient.marie@gmail.com', description: 'E-mail')]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        #[OA\Property(type: 'string', minLength: 8, example: 'SecurePassword123!', description: 'Mot de passe')]
        public readonly string $password,

        #[Assert\Length(max: 50)]
        #[OA\Property(type: 'string', maxLength: 50, nullable: true, example: '+243998887766', description: 'Téléphone')]
        public readonly ?string $phone,

        #[Assert\NotBlank]
        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, example: 'Marie Zawadi', description: 'Nom complet')]
        public readonly string $fullName,

        #[Assert\Url]
        #[Assert\Length(max: 500)]
        #[OA\Property(type: 'string', format: 'uri', maxLength: 500, nullable: true, example: 'https://storage.diabcare.com/avatars/marie.jpg', description: 'Avatar URL')]
        public readonly ?string $avatarUrl,

        #[Assert\NotNull]
        #[OA\Property(type: 'string', example: 'FEMALE', description: 'Genre')]
        public readonly Gender $gender,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', maxLength: 10, example: 'fr', description: 'Locale')]
        public readonly string $locale,

        #[Assert\Date]
        #[OA\Property(type: 'string', format: 'date', nullable: true, example: '1995-06-15', description: 'Date de naissance (YYYY-MM-DD)')]
        public readonly ?string $dateOfBirth,

        #[Assert\Length(max: 150)]
        #[OA\Property(type: 'string', maxLength: 150, nullable: true, example: 'Bukavu', description: 'Lieu de naissance')]
        public readonly ?string $placeOfBirth,

        #[Assert\Length(max: 10)]
        #[OA\Property(type: 'string', maxLength: 10, nullable: true, example: 'O+', description: 'Groupe sanguin')]
        public readonly ?string $bloodType,

        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(type: 'string', nullable: true, example: '168.5', description: 'Taille en centimètres')]
        public readonly ?string $heightCm,

        #[Assert\Length(max: 255)]
        #[OA\Property(type: 'string', maxLength: 255, nullable: true, example: '45 Boulevard Kanyamuhanga', description: 'Rue')]
        public readonly ?string $street,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'Goma', description: 'Ville')]
        public readonly ?string $city,

        #[Assert\Length(max: 20)]
        #[OA\Property(type: 'string', maxLength: 20, nullable: true, example: '00243', description: 'Code postal')]
        public readonly ?string $postalCode,

        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, nullable: true, example: 'RDC', description: 'Pays')]
        public readonly ?string $country
    ) {}
}
