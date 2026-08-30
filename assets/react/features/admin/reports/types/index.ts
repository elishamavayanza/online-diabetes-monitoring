export interface StatisticValue {
    value: number | null;
    previousValue?: number | null;
    changePercent?: number | null;
    unit?: string | null;
}

export interface DistributionItem {
    label: string;
    count: number;
    percentage: number;
}

export interface ReportPeriod {
    from: string;
    to: string;
    previousFrom: string;
    previousTo: string;
    preset: 'custom' | 'month' | 'quarter' | 'year';
}

export interface TrendPoint {
    date: string;
    value: number;
}

export interface TrendSeries {
    label: string;
    unit?: string | null;
    points: TrendPoint[];
}

export interface DemographicsReport {
    totalPatients: StatisticValue;
    activePatients: StatisticValue;
    newPatients: StatisticValue;
    genderDistribution: DistributionItem[];
    ageGroups: DistributionItem[];
}

export interface HealthStatusReport {
    averageGlucose: StatisticValue;
    glucoseMeasurements: StatisticValue;
    glucoseRanges: DistributionItem[];
    averageHbA1c: StatisticValue;
    hba1cMeasurements: StatisticValue;
    averageSystolic: StatisticValue;
    averageDiastolic: StatisticValue;
    averageBmi: StatisticValue;
    averageWeightKg: StatisticValue;
}

export interface MedicalActivityReport {
    totalAppointments: StatisticValue;
    completedAppointments: StatisticValue;
    cancelledAppointments: StatisticValue;
    appointmentsByStatus: DistributionItem[];
    diagnosesCount: StatisticValue;
    openMedicalRecords: StatisticValue;
    closedMedicalRecords: StatisticValue;
}

export interface TreatmentReport {
    activePrescriptions: StatisticValue;
    newPrescriptions: StatisticValue;
    adherenceRate: StatisticValue;
    totalIntakes: StatisticValue;
    intakesByStatus: DistributionItem[];
}

export interface LifestyleReport {
    totalMeals: StatisticValue;
    mealsByType: DistributionItem[];
    physicalActivitySessions: StatisticValue;
    totalActivityMinutes: StatisticValue;
    averageActivityMinutes: StatisticValue;
}

export interface TrendsReport {
    patientsWithMeasurements: StatisticValue;
    measurementComplianceRate: StatisticValue;
    series: TrendSeries[];
}

export interface OrganizationReport {
    organizationId: string;
    organizationName: string;
    period: ReportPeriod;
    demographics: DemographicsReport;
    healthStatus: HealthStatusReport;
    medicalActivity: MedicalActivityReport;
    treatments: TreatmentReport;
    lifestyle: LifestyleReport;
    trends: TrendsReport;
    generatedAt: string;
}

export type PeriodPreset = 'month' | 'quarter' | 'year';

export interface ReportFilters {
    period?: PeriodPreset;
    from?: string;
    to?: string;
}
