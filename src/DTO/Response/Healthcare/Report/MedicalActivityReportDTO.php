<?php

namespace App\DTO\Response\Healthcare\Report;

use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Activité médicale de l\'organisation')]
class MedicalActivityReportDTO
{
    /**
     * @param DistributionItemDTO[] $appointmentsByStatus
     */
    public function __construct(
        public readonly StatisticValueDTO $totalAppointments,
        public readonly StatisticValueDTO $completedAppointments,
        public readonly StatisticValueDTO $cancelledAppointments,
        public readonly array $appointmentsByStatus,
        public readonly StatisticValueDTO $diagnosesCount,
        public readonly StatisticValueDTO $openMedicalRecords,
        public readonly StatisticValueDTO $closedMedicalRecords,
    ) {}
}
