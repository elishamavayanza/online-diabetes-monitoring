<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\Entity\Identity\Patient;
use App\Enum\Medical\FollowUpReportElement;
use App\Repository\Identity\PatientRepository;
use App\Repository\Medical\DiagnosisRepository;
use App\Repository\Medical\PatientFollowUpReportRepository;
use App\Mapper\Medical\PatientFollowUpReportMapper;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PatientFollowUpReportService
{
    public function __construct(
        private readonly PatientRepository $patientRepository,
        private readonly PatientFollowUpReportRepository $reportRepository,
        private readonly DiagnosisRepository $diagnosisRepository,
        private readonly PatientFollowUpReportMapper $mapper,
        private readonly SecurityServiceInterface $securityService,
    ) {}

    /**
     * @param string[] $elementValues
     */
    public function generateReport(string $patientId, ?string $from, ?string $to, array $elementValues): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if ($patient === null) {
                return $feedback
                    ->setErrorFlushDescription('Patient introuvable.')
                    ->setStatus(404)
                    ->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

            $elements = FollowUpReportElement::fromValues($elementValues);
            if ($elements === []) {
                return $feedback
                    ->setErrorFlushDescription('Veuillez sélectionner au moins un élément à inclure dans le rapport.')
                    ->setStatus(400)
                    ->autoInitFlush();
            }

            [$fromDate, $toDate] = $this->resolvePeriod($from, $to);
            $sections = $this->collectSections($patient, $fromDate, $toDate, $elements);
            $clinicianName = $this->securityService->getCurrentUser()->getFullName();
            $diabetesType = $this->resolveDiabetesType($patient);

            $report = $this->mapper->mapToReport(
                $patient,
                $fromDate,
                $toDate,
                $elements,
                $sections,
                $clinicianName,
                $diabetesType,
            );

            return $feedback
                ->setData($report)
                ->setFlushDescription('Rapport de suivi généré avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->setStatus(403)
                ->autoInitFlush();
        } catch (\InvalidArgumentException $e) {
            return $feedback
                ->setErrorFlushDescription($e->getMessage())
                ->setStatus(400)
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur lors de la génération du rapport : ' . $e->getMessage())
                ->setStatus(500)
                ->autoInitFlush();
        }
    }

    /**
     * @return array{0: DateTimeImmutable, 1: DateTimeImmutable}
     */
    private function resolvePeriod(?string $from, ?string $to): array
    {
        $now = new DateTimeImmutable('today');
        $fromDate = $from
            ? DateTimeImmutable::createFromFormat('Y-m-d', $from)?->setTime(0, 0)
            : $now->modify('-29 days')->setTime(0, 0);
        $toDate = $to
            ? DateTimeImmutable::createFromFormat('Y-m-d', $to)?->setTime(23, 59, 59)
            : $now->setTime(23, 59, 59);

        if (!$fromDate || !$toDate || $fromDate > $toDate) {
            throw new \InvalidArgumentException('Période invalide : la date de début doit être antérieure ou égale à la date de fin.');
        }

        return [$fromDate, $toDate];
    }

    /**
     * @param FollowUpReportElement[] $elements
     */
    private function collectSections(Patient $patient, DateTimeImmutable $from, DateTimeImmutable $to, array $elements): array
    {
        $sections = [];

        foreach ($elements as $element) {
            $sections[match ($element) {
                FollowUpReportElement::GLUCOSE => 'glucose',
                FollowUpReportElement::HBA1C => 'hba1c',
                FollowUpReportElement::BLOOD_PRESSURE => 'bloodPressure',
                FollowUpReportElement::WEIGHT => 'weight',
                FollowUpReportElement::TREATMENT => 'treatment',
                FollowUpReportElement::PHYSICAL_ACTIVITY => 'physicalActivity',
                FollowUpReportElement::NUTRITION => 'nutrition',
                FollowUpReportElement::LABORATORY => 'laboratory',
            }] = match ($element) {
                FollowUpReportElement::GLUCOSE => $this->mapper->mapGlucoseSection(
                    $this->reportRepository->glucoseStats($patient, $from, $to)
                ),
                FollowUpReportElement::HBA1C => $this->mapper->mapHbA1cSection(
                    $this->reportRepository->hba1cStats($patient, $from, $to)
                ),
                FollowUpReportElement::BLOOD_PRESSURE => $this->mapper->mapBloodPressureSection(
                    $this->reportRepository->bloodPressureStats($patient, $from, $to)
                ),
                FollowUpReportElement::WEIGHT => $this->mapper->mapWeightSection(
                    $this->reportRepository->weightStats($patient, $from, $to)
                ),
                FollowUpReportElement::TREATMENT => $this->mapper->mapTreatmentSection(
                    $this->reportRepository->treatmentStats($patient, $from, $to)
                ),
                FollowUpReportElement::PHYSICAL_ACTIVITY => $this->mapper->mapPhysicalActivitySection(
                    $this->reportRepository->physicalActivityStats($patient, $from, $to)
                ),
                FollowUpReportElement::NUTRITION => $this->mapper->mapNutritionSection(
                    $this->reportRepository->nutritionStats($patient, $from, $to)
                ),
                FollowUpReportElement::LABORATORY => $this->mapper->mapLaboratorySection(
                    $this->reportRepository->laboratoryResults($patient, $from, $to)
                ),
            };
        }

        return $sections;
    }

    private function resolveDiabetesType(Patient $patient): ?string
    {
        $diagnoses = $this->diagnosisRepository->findActiveByPatient($patient);

        foreach ($diagnoses as $diagnosis) {
            $name = mb_strtolower($diagnosis->getConditionName() ?? '');
            if (str_contains($name, 'diabète') || str_contains($name, 'diabete') || str_contains($name, 'diabetes')) {
                return $diagnosis->getConditionName();
            }
        }

        return null;
    }
}
