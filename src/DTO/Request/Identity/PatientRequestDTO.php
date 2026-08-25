<?php

namespace App\DTO\Request\Identity;

use App\Entity\Common\Gender;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'PatientRequestDTO',
    description: 'Structure des données requises pour la mise à jour du profil patient'
)]
class PatientRequestDTO
{
    public function __construct(
        #[Assert\Length(max: 50)]
        #[OA\Property(description: 'Téléphone', type: 'string', example: '+243998887766', nullable: true, maxLength: 50)]
        public readonly ?string $phone,

        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Nom complet', type: 'string', example: 'Marie Zawadi', nullable: true, maxLength: 150)]
        public readonly ?string $fullName,

        #[Assert\Image(
            maxSize: '2M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
        )]
        #[OA\Property(description: 'Photo de profil (avatar)', type: 'string', format: 'binary', nullable: true)]
        public readonly ?UploadedFile $avatarFile,

        #[OA\Property(description: 'Genre', type: 'string', example: 'FEMALE', nullable: true)]
        public readonly ?Gender $gender,

        #[Assert\Length(max: 10)]
        #[OA\Property(description: 'Locale', type: 'string', example: 'fr', nullable: true, maxLength: 10)]
        public readonly ?string $locale,

        #[Assert\Date]
        #[OA\Property(description: 'Date de naissance (YYYY-MM-DD)', type: 'string', format: 'date', example: '1995-06-15', nullable: true)]
        public readonly ?string $dateOfBirth,

        #[Assert\Length(max: 150)]
        #[OA\Property(description: 'Lieu de naissance', type: 'string', example: 'Bukavu', nullable: true, maxLength: 150)]
        public readonly ?string $placeOfBirth,

        #[Assert\Length(max: 10)]
        #[OA\Property(description: 'Groupe sanguin', type: 'string', example: 'O+', nullable: true, maxLength: 10)]
        public readonly ?string $bloodType,

        #[Assert\Regex('/^\d+(\.\d{1,2})?$/')]
        #[OA\Property(description: 'Taille en centimètres', type: 'string', example: '168.5', nullable: true)]
        public readonly ?string $heightCm,

        #[Assert\Length(max: 255)]
        #[OA\Property(description: 'Rue', type: 'string', example: '45 Boulevard Kanyamuhanga', nullable: true, maxLength: 255)]
        public readonly ?string $street,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Ville', type: 'string', example: 'Goma', nullable: true, maxLength: 100)]
        public readonly ?string $city,

        #[Assert\Length(max: 20)]
        #[OA\Property(description: 'Code postal', type: 'string', example: '00243', nullable: true, maxLength: 20)]
        public readonly ?string $postalCode,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'Pays', type: 'string', example: 'RDC', nullable: true, maxLength: 100)]
        public readonly ?string $country,

        #[Assert\Length(max: 100)]
        #[OA\Property(description: 'État / Province', type: 'string', example: 'Nord-Kivu', nullable: true, maxLength: 100)]
        public readonly ?string $state
    ) {}
}
