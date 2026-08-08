<?php

namespace App\Mapper\Medical;

use App\DTO\Request\Medical\MedicalNoteRequestDTO;
use App\DTO\Response\Medical\MedicalNoteResponseDTO;
use App\Entity\Medical\MedicalNote;
use App\Entity\Medical\MedicalRecord;
use App\Entity\Identity\User;

class MedicalNoteMapper
{
    public function mapRequestToEntity(MedicalNoteRequestDTO $dto, MedicalRecord $medicalRecord, User $author, ?MedicalNote $note = null): MedicalNote
    {
        $note ??= new MedicalNote();

        $note->setMedicalRecord($medicalRecord);
        $note->setAuthor($author);
        $note->setContent($dto->content);
        $note->setNotedAt($dto->notedAt);

        return $note;
    }

    public function mapEntityToResponse(MedicalNote $note): MedicalNoteResponseDTO
    {
        return MedicalNoteResponseDTO::fromEntity($note);
    }
}
