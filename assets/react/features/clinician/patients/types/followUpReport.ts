export interface ReportMeasurementStats {
    average: number | null;
    minimum: number | null;
    maximum: number | null;
    count: number;
    unit?: string | null;
}

export interface DistributionItem {
    label: string;
    count: number;
    percentage: number;
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

export interface PatientFollowUpReportHeader {
    patientId: string;
    patientFullName: string;
    dateOfBirth?: string | null;
    diabetesType?: string | null;
    clinicianName?: string | null;
    organizationName?: string | null;
    avatarUrl?: string | null;
}

export interface PatientFollowUpReportPeriod {
    from: string;
    to: string;
}

export interface GlucoseReportSection {
    stats: ReportMeasurementStats;
    ranges: DistributionItem[];
    trend?: TrendSeries | null;
}

export interface HbA1cReportSection {
    stats: ReportMeasurementStats;
    trend?: TrendSeries | null;
}

export interface BloodPressureReportSection {
    systolic: ReportMeasurementStats;
    diastolic: ReportMeasurementStats;
    trends: TrendSeries[];
}

export interface WeightReportSection {
    weight: ReportMeasurementStats;
    bmi: ReportMeasurementStats;
    weightTrend?: TrendSeries | null;
    bmiTrend?: TrendSeries | null;
}

export interface TreatmentReportSection {
    activePrescriptions: number;
    adherenceRate: number | null;
    totalIntakes: number;
    intakesByStatus: DistributionItem[];
}

export interface PhysicalActivityReportSection {
    sessions: number;
    totalMinutes: number;
    averageMinutes: number | null;
    trend?: TrendSeries | null;
}

export interface NutritionReportSection {
    totalMeals: number;
    mealsByType: DistributionItem[];
}

export interface LaboratoryReportItem {
    testName: string;
    labName?: string | null;
    measuredAt: string;
    hasFile: boolean;
}

export interface LaboratoryReportSection {
    count: number;
    results: LaboratoryReportItem[];
}

export type FollowUpReportElementId =
    | 'glucose'
    | 'hba1c'
    | 'blood_pressure'
    | 'weight'
    | 'treatment'
    | 'physical_activity'
    | 'nutrition'
    | 'laboratory';

export interface PatientFollowUpReport {
    header: PatientFollowUpReportHeader;
    period: PatientFollowUpReportPeriod;
    selectedElements: FollowUpReportElementId[];
    hasData: boolean;
    glucose?: GlucoseReportSection | null;
    hba1c?: HbA1cReportSection | null;
    bloodPressure?: BloodPressureReportSection | null;
    weight?: WeightReportSection | null;
    treatment?: TreatmentReportSection | null;
    physicalActivity?: PhysicalActivityReportSection | null;
    nutrition?: NutritionReportSection | null;
    laboratory?: LaboratoryReportSection | null;
    generatedAt: string;
}

export interface FollowUpReportFilters {
    from: string;
    to: string;
}
