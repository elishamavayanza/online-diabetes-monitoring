<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\DiagnosisRequestDTO;
use App\Mapper\Medical\DiagnosisMapper;
use App\Repository\Medical\DiagnosisRepository;
use App\Repository\Medical\MedicalRecordRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DiagnosisService
{
    public function __construct(
        private readonly DiagnosisRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly MedicalRecordRepository $medicalRecordRepository,
        private readonly DiagnosisMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function getById(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $diagnosis = $this->repository->find($id);

            if (!$diagnosis) {
                return $feedback
                    ->setErrorFlushDescription('Diagnostic introuvable.')
                    ->autoInitFlush();
            }

            // Vérifier l'accès via le patient lié au diagnostic
            $this->securityService->checkPatientAccess(
                $diagnosis->getPatient(),
                SecurityAction::VIEW_MEDICAL_RECORD
            );

            $feedback
                ->setData($this->mapper->mapEntityToResponse($diagnosis))
                ->setFlushDescription('Diagnostic récupéré avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function getByPatient(int $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->autoInitFlush();
            }

            // Vérifier l'accès aux données du patient
            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::VIEW_MEDICAL_RECORD
            );

            $diagnoses = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map(
                fn($diagnosis) => $this->mapper->mapEntityToResponse($diagnosis),
                $diagnoses
            );

            $feedback
                ->setData($responseDTOs)
                ->setFlushDescription('Diagnostics récupérés avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function create(DiagnosisRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($dto->patientId);

            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->autoInitFlush();
            }

            $doctor = $this->professionalRepository->find($dto->doctorId);

            if (!$doctor) {
                return $feedback
                    ->setErrorFlushDescription('Médecin introuvable.')
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::CREATE_DIAGNOSIS
            );

            $medicalRecord = $dto->medicalRecordId
                ? $this->medicalRecordRepository->find($dto->medicalRecordId)
                : null;

            $diagnosis = $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $doctor,
                $medicalRecord
            );

            $this->entityManager->persist($diagnosis);
            $this->entityManager->flush();

            $feedback
                ->setData($this->mapper->mapEntityToResponse($diagnosis))
                ->setFlushDescription('Diagnostic créé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function update(int $id, DiagnosisRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $diagnosis = $this->repository->find($id);

            if (!$diagnosis) {
                return $feedback
                    ->setErrorFlushDescription('Diagnostic introuvable.')
                    ->autoInitFlush();
            }

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->autoInitFlush();
            }

            $doctor = $this->professionalRepository->find($dto->doctorId);
            if (!$doctor) {
                return $feedback
                    ->setErrorFlushDescription('Médecin introuvable.')
                    ->autoInitFlush();
            }

            // Vérifier l'autorisation de modification
            $this->securityService->checkPatientAccess(
                $patient,
                SecurityAction::UPDATE_DIAGNOSIS
            );

            $medicalRecord = $dto->medicalRecordId
                ? $this->medicalRecordRepository->find($dto->medicalRecordId)
                : null;

            // Mise à jour de l'entité via le mapper ou directement
            $updatedDiagnosis = $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $doctor,
                $medicalRecord,
                $diagnosis // Si votre mapper gère la mise à jour sur une entité existante
            );

            $this->entityManager->flush();

            $feedback
                ->setData($this->mapper->mapEntityToResponse($updatedDiagnosis))
                ->setFlushDescription('Diagnostic mis à jour avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $diagnosis = $this->repository->find($id);

            if (!$diagnosis) {
                return $feedback
                    ->setErrorFlushDescription('Diagnostic introuvable.')
                    ->autoInitFlush();
            }

            // Vérifier l'accès sur le patient associé pour la suppression (souvent lié à UPDATE_DIAGNOSIS ou DELETE)
            $this->securityService->checkPatientAccess(
                $diagnosis->getPatient(),
                SecurityAction::UPDATE_DIAGNOSIS
            );

            $this->entityManager->remove($diagnosis);
            $this->entityManager->flush();

            $feedback
                ->setFlushDescription('Diagnostic supprimé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }
}
