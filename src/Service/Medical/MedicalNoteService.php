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
                    ->setErrorFlushDescription('Dossier médical introuvable.')
                    ->autoInitFlush();
            }

            $patient = $medicalRecord->getPatient();

            if ($patient === null) {
                return $feedback
                    ->setErrorFlushDescription('Le dossier médical n’est associé à aucun patient.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccessAndOrganization(
                $patient,
                SecurityAction::CREATE_MEDICAL_NOTE
            );

            $author = $this->userRepository->find($dto->authorId);

            if (!$author) {
                return $feedback
                    ->setErrorFlushDescription('Auteur introuvable.')
                    ->autoInitFlush();
            }

            $note = $this->mapper->mapRequestToEntity(
                $dto,
                $medicalRecord,
                $patient,
                $author
            );

            $this->entityManager->persist($note);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($note))
                ->setFlushDescription('Note médicale ajoutée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function getByMedicalRecord(string $medicalRecordId): Feedback
    {
        $feedback = new Feedback();

        try {
            $medicalRecord = $this->medicalRecordRepository->find($medicalRecordId);

            if (!$medicalRecord) {
                return $feedback->setErrorFlushDescription('Dossier médical introuvable.')->autoInitFlush();
            }

            $patient = $medicalRecord->getPatient();
            if ($patient) {
                $this->securityService->checkPatientAccessAndOrganization($patient, SecurityAction::VIEW_MEDICAL_NOTES);
            }

            $notes = $this->repository->findBy(['medicalRecord' => $medicalRecord]);
            $responseDTOs = array_map(fn($note) => $this->mapper->mapEntityToResponse($note), $notes);

            return $feedback
                ->setData($responseDTOs)
                ->setFlushDescription('Notes médicales récupérées avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function getById(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $note = $this->repository->find($id);

            if (!$note) {
                return $feedback->setErrorFlushDescription('Note médicale introuvable.')->autoInitFlush();
            }

            $patient = $note->getPatient();
            if ($patient) {
                $this->securityService->checkPatientAccessAndOrganization($patient, SecurityAction::VIEW_MEDICAL_NOTES);
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($note))
                ->setFlushDescription('Note médicale récupérée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function update(string $id, MedicalNoteRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $note = $this->repository->find($id);

            if (!$note) {
                return $feedback->setErrorFlushDescription('Note médicale introuvable.')->autoInitFlush();
            }

            $patient = $note->getPatient();
            if ($patient) {
                $this->securityService->checkPatientAccessAndOrganization($patient, SecurityAction::EDIT_MEDICAL_NOTE);
            }

            $medicalRecord = $this->medicalRecordRepository->find($dto->medicalRecordId);
            if (!$medicalRecord) {
                return $feedback->setErrorFlushDescription('Dossier médical introuvable.')->autoInitFlush();
            }

            $author = $this->userRepository->find($dto->authorId);
            if (!$author) {
                return $feedback->setErrorFlushDescription('Auteur introuvable.')->autoInitFlush();
            }

            $this->mapper->mapRequestToEntity($dto, $medicalRecord, $patient, $author, $note);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($note))
                ->setFlushDescription('Note médicale mise à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $note = $this->repository->find($id);

            if (!$note) {
                return $feedback->setErrorFlushDescription('Note médicale introuvable.')->autoInitFlush();
            }

            $patient = $note->getPatient();
            if ($patient) {
                $this->securityService->checkPatientAccessAndOrganization($patient, SecurityAction::DELETE_MEDICAL_NOTE);
            }

            $this->entityManager->remove($note);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Note médicale supprimée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
}
