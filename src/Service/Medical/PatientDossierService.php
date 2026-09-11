<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Response\Identity\PatientResponseDTO;
use App\DTO\Response\Nutrition\MealItemResponseDTO;
use App\Entity\Identity\Patient;
use App\Mapper\Appointment\AppointmentMapper;
use App\Mapper\Medical\BloodGlucoseMeasurementMapper;
use App\Mapper\Medical\BloodPressureMeasurementMapper;
use App\Mapper\Medical\DiagnosisMapper;
use App\Mapper\Medical\HbA1cMeasurementMapper;
use App\Mapper\Medical\LaboratoryResultMapper;
use App\Mapper\Medical\MedicalNoteMapper;
use App\Mapper\Medical\MedicalRecordMapper;
use App\Mapper\Medical\PhysicalActivityMeasurementMapper;
use App\Mapper\Medical\WeightMeasurementMapper;
use App\Mapper\Nutrition\MealMapper;
use App\Mapper\Patient\AllergyMapper;
use App\Mapper\Patient\EmergencyContactMapper;
use App\Mapper\Patient\MedicalConsentMapper;
use App\Mapper\Treatment\InsulinInjectionMapper;
use App\Mapper\Treatment\PrescriptionItemMapper;
use App\Mapper\Treatment\PrescriptionMapper;
use App\Mapper\Treatment\PrescriptionVersionMapper;
use App\Repository\Appointment\AppointmentRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Medical\BloodGlucoseMeasurementRepository;
use App\Repository\Medical\BloodPressureMeasurementRepository;
use App\Repository\Medical\DiagnosisRepository;
use App\Repository\Medical\HbA1cMeasurementRepository;
use App\Repository\Medical\LaboratoryResultRepository;
use App\Repository\Medical\MedicalNoteRepository;
use App\Repository\Medical\MedicalRecordRepository;
use App\Repository\Medical\PhysicalActivityMeasurementRepository;
use App\Repository\Medical\WeightMeasurementRepository;
use App\Repository\Nutrition\MealRepository;
use App\Repository\Patient\AllergyRepository;
use App\Repository\Patient\EmergencyContactRepository;
use App\Repository\Patient\MedicalConsentRepository;
use App\Repository\Treatment\InsulinInjectionRepository;
use App\Repository\Treatment\PrescriptionItemRepository;
use App\Repository\Treatment\PrescriptionRepository;
use App\Repository\Treatment\PrescriptionVersionRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\Common\ListQueryParams;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Agrège le dossier patient en une seule réponse API pour supprimer
 * le fan-out HTTP (16–18 appels) côté frontend.
 */
class PatientDossierService
{
    public function __construct(
        private readonly PatientRepository $patientRepository,
        private readonly MedicalRecordRepository $medicalRecordRepository,
        private readonly MedicalRecordMapper $medicalRecordMapper,
        private readonly AllergyRepository $allergyRepository,
        private readonly AllergyMapper $allergyMapper,
        private readonly DiagnosisRepository $diagnosisRepository,
        private readonly DiagnosisMapper $diagnosisMapper,
        private readonly EmergencyContactRepository $emergencyContactRepository,
        private readonly EmergencyContactMapper $emergencyContactMapper,
        private readonly MedicalConsentRepository $medicalConsentRepository,
        private readonly MedicalConsentMapper $medicalConsentMapper,
        private readonly MedicalNoteRepository $medicalNoteRepository,
        private readonly MedicalNoteMapper $medicalNoteMapper,
        private readonly PrescriptionRepository $prescriptionRepository,
        private readonly PrescriptionMapper $prescriptionMapper,
        private readonly PrescriptionItemRepository $prescriptionItemRepository,
        private readonly PrescriptionItemMapper $prescriptionItemMapper,
        private readonly PrescriptionVersionRepository $prescriptionVersionRepository,
        private readonly PrescriptionVersionMapper $prescriptionVersionMapper,
        private readonly AppointmentRepository $appointmentRepository,
        private readonly AppointmentMapper $appointmentMapper,
        private readonly BloodGlucoseMeasurementRepository $bloodGlucoseRepository,
        private readonly BloodGlucoseMeasurementMapper $bloodGlucoseMapper,
        private readonly BloodPressureMeasurementRepository $bloodPressureRepository,
        private readonly BloodPressureMeasurementMapper $bloodPressureMapper,
        private readonly HbA1cMeasurementRepository $hba1cRepository,
        private readonly HbA1cMeasurementMapper $hba1cMapper,
        private readonly WeightMeasurementRepository $weightRepository,
        private readonly WeightMeasurementMapper $weightMapper,
        private readonly PhysicalActivityMeasurementRepository $activityRepository,
        private readonly PhysicalActivityMeasurementMapper $activityMapper,
        private readonly LaboratoryResultRepository $laboratoryRepository,
        private readonly LaboratoryResultMapper $laboratoryMapper,
        private readonly InsulinInjectionRepository $insulinInjectionRepository,
        private readonly InsulinInjectionMapper $insulinInjectionMapper,
        private readonly MealRepository $mealRepository,
        private readonly MealMapper $mealMapper,
        private readonly SecurityServiceInterface $securityService,
    ) {
    }

    public function getDossier(string $patientId, ?ListQueryParams $listParams = null): Feedback
    {
        $feedback = new Feedback();
        $listParams ??= new ListQueryParams(
            from: new DateTimeImmutable('-365 days'),
            to: null,
            limit: 500,
        );

        try {
            $patients = $this->patientRepository->findWithMembershipsByIds(Patient::class, [$patientId]);
            $patient = $patients[0] ?? $this->patientRepository->find($patientId);
            if (!$patient instanceof Patient) {
                return $feedback->setErrorFlushDescription('Patient introuvable.')->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $recordEntity = $this->medicalRecordRepository->findOpenRecordForPatient($patient)
                ?? $this->medicalRecordRepository->findLatestRecordForPatient($patient);

            $from = $listParams->from;
            $to = $listParams->to;
            $limit = $listParams->limit;

            $meals = $this->mealRepository->findByPatientWithIssuerAndItems($patient, $from, $to, $limit);
            $mealDtos = array_map(fn ($m) => $this->mealMapper->mapEntityToResponse($m, true), $meals);
            $mealItems = [];
            foreach ($meals as $meal) {
                foreach ($meal->getMealItems() as $item) {
                    if ($item->getDeletedAt() !== null) {
                        continue;
                    }
                    $mealItems[] = MealItemResponseDTO::fromEntity($item);
                }
            }

            $prescriptions = $this->prescriptionRepository->findAllByPatient($patient);
            $prescriptionIds = array_map(static fn ($p) => $p->getId(), $prescriptions);
            $prescriptionItems = $this->prescriptionItemRepository->findByPrescriptionIds($prescriptionIds);
            $prescriptionVersions = $this->prescriptionVersionRepository->findByPrescriptionIds($prescriptionIds);

            $notes = [];
            if ($recordEntity !== null) {
                $notes = array_map(
                    [$this->medicalNoteMapper, 'mapEntityToResponse'],
                    $this->medicalNoteRepository->findByMedicalRecord($recordEntity)
                );
            }

            $data = [
                'profile' => PatientResponseDTO::fromEntity($patient),
                'record' => $recordEntity ? $this->medicalRecordMapper->mapEntityToResponse($recordEntity) : null,
                'allergies' => array_map(
                    [$this->allergyMapper, 'mapEntityToResponse'],
                    $this->allergyRepository->findBy(['patient' => $patient])
                ),
                'diagnoses' => array_map(
                    [$this->diagnosisMapper, 'mapEntityToResponse'],
                    $this->diagnosisRepository->findBy(['patient' => $patient])
                ),
                'emergencyContacts' => array_map(
                    [$this->emergencyContactMapper, 'mapEntityToResponse'],
                    $this->emergencyContactRepository->findBy(['patient' => $patient])
                ),
                'consents' => array_map(
                    [$this->medicalConsentMapper, 'mapEntityToResponse'],
                    $this->medicalConsentRepository->findBy(['patient' => $patient])
                ),
                'notes' => $notes,
                'prescriptions' => array_map([$this->prescriptionMapper, 'mapEntityToResponse'], $prescriptions),
                'prescriptionItems' => $this->prescriptionItemMapper->mapEntitiesToResponses($prescriptionItems),
                'prescriptionVersions' => $this->prescriptionVersionMapper->mapEntitiesToResponses($prescriptionVersions),
                'appointments' => array_map(
                    [$this->appointmentMapper, 'mapEntityToResponse'],
                    $this->appointmentRepository->findByPatientOrderedByScheduledAt($patient)
                ),
                'meals' => $mealDtos,
                'mealItems' => $mealItems,
                'measurements' => [
                    'bloodGlucose' => array_map(
                        [$this->bloodGlucoseMapper, 'mapEntityToResponse'],
                        $this->bloodGlucoseRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'bloodPressure' => array_map(
                        [$this->bloodPressureMapper, 'mapEntityToResponse'],
                        $this->bloodPressureRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'hba1c' => array_map(
                        [$this->hba1cMapper, 'mapEntityToResponse'],
                        $this->hba1cRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'weight' => array_map(
                        [$this->weightMapper, 'mapEntityToResponse'],
                        $this->weightRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'physicalActivity' => array_map(
                        [$this->activityMapper, 'mapEntityToResponse'],
                        $this->activityRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'laboratoryResults' => array_map(
                        [$this->laboratoryMapper, 'mapEntityToResponse'],
                        $this->laboratoryRepository->findByPatientWithIssuer($patient, $from, $to, $limit)
                    ),
                    'insulinInjections' => array_map(
                        [$this->insulinInjectionMapper, 'mapEntityToResponse'],
                        $this->insulinInjectionRepository->findByPatient($patient)
                    ),
                ],
            ];

            return $feedback
                ->setData($data)
                ->setFlushDescription('Dossier patient agrégé récupéré avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
}
