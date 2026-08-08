<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\MedicalNoteRequestDTO;
use App\Mapper\Medical\MedicalNoteMapper;
use App\Repository\Identity\UserRepository;
use App\Repository\Medical\MedicalNoteRepository;
use App\Repository\Medical\MedicalRecordRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MedicalNoteService
{
    public function __construct(
        private readonly MedicalNoteRepository $repository,
        private readonly MedicalRecordRepository $medicalRecordRepository,
        private readonly UserRepository $userRepository,
        private readonly MedicalNoteMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function create(MedicalNoteRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $medicalRecord = $this->medicalRecordRepository->find(
                $dto->medicalRecordId
            );

            if (!$medicalRecord) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Dossier médical introuvable.'
                    )
                    ->autoInitFlush();
            }

            $patient = $medicalRecord->getPatient();

            if ($patient === null) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Le dossier médical n’est associé à aucun patient.'
                    )
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccessAndOrganization(
                $patient,
                SecurityAction::CREATE_MEDICAL_NOTE
            );

            $author = $this->userRepository->find($dto->authorId);

            if (!$author) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Auteur introuvable.'
                    )
                    ->autoInitFlush();
            }

            $note = $this->mapper->mapRequestToEntity(
                $dto,
                $medicalRecord,
                $author
            );

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($note)
                )
                ->setFlushDescription(
                    'Note médicale ajoutée avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
