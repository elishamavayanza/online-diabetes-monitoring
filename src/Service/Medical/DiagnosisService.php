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
}
