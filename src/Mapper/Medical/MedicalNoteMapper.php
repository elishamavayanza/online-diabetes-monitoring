<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\MedicalNoteRequestDTO;
use App\DTO\Response\Medical\MedicalNoteResponseDTO;
use App\Entity\Medical\MedicalNote;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Identity\User;
use App\Entity\Identity\Patient;

class MedicalNoteMapper
{
    public function mapRequestToEntity(
        MedicalNoteRequestDTO $dto,
        MedicalRecord $medicalRecord,
        Patient $patient,
        User $author,
        ?MedicalNote $note = null
    ): MedicalNote {
        $note ??= new MedicalNote();

        // Champs spécifiques à MedicalNote
        $note->setMedicalRecord($medicalRecord);
        $note->setContent($dto->content);

        $notedAt = $dto->notedAt ?? new \DateTimeImmutable();
        $note->setNotedAt($notedAt);

        // Champs hérités de PatientCommonOperation (obligatoires en base de données)
        $note->setPatient($patient);
        $note->setIssuer($author);        // Remplit la colonne issuer_id
        $note->setMeasuredAt($notedAt);   // Remplit la colonne measured_at

        // Auteur spécifique à la note (si l'entité MedicalNote utilise cette relation en plus de l'issuer)
        if (method_exists($note, 'setAuthor')) {
            $note->setAuthor($author);
        }

        return $note;
    }

    public function mapEntityToResponse(MedicalNote $note): MedicalNoteResponseDTO
    {
        return MedicalNoteResponseDTO::fromEntity($note);
    }
}
