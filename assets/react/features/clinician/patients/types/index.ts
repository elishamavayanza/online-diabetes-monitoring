export interface ClinicianPatient {
    id: string;
    nom: string;
    derniereConsultation?: string;
    prochainRendezVous?: string;
    statut: 'Active' | 'Inactive';
    avatarUrl?: string;
    dateNaissance?: string;
    telephone?: string;
    email?: string;
    bloodType?: string;
    heightCm?: number;
    hasMedicalRecord?: boolean;
    medicalRecordStatus: 'none' | 'closed' | 'open';
}

export interface MedicalRecord {
    id: string;
    patientId: string;
    organizationId: string;
    status: 'open' | 'closed' | 'none';
    heightCm?: number;
    weightKg?: number;
    bloodType?: string;
    allergies?: string[];
    diagnoses?: string[];
    openedAt?: string;
    closedAt?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface PatientProfile {
    id: string;
    fullName: string;
    email: string;
    phone?: string;
    avatarUrl?: string;
    dateOfBirth?: string;
    bloodType?: string;
    heightCm?: number;
    organizationId?: string;
    organizationName?: string;
    status?: string;
}

export interface PatientAllergy {
    id: string;
    name: string;
    severity?: string;
    reaction?: string;
    notes?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientDiagnosis {
    id: string;
    conditionName: string;
    description?: string;
    diagnosedAt?: string;
    status?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientEmergencyContact {
    id: string;
    fullName: string;
    relationship?: string;
    phone?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientMedicalNote {
    id: string;
    content: string;
    createdAt: string;
    notedAt?: string;
    authorId?: string;
    authorName?: string;
}

export interface PatientPrescription {
    id: string;
    status: string;
    startDate?: string;
    endDate?: string;
    notes?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PrescriptionItem {
    id: string;
    prescriptionId: string;
    medicationId: string;
    medicationName?: string;
    medicationCategory?: string;
    insulinType?: string | null;
    concentration?: string | null;
    dosage: string;
    quantity: string;
    morning: boolean;
    noon: boolean;
    evening: boolean;
    instructions?: string;
    createdAt?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PrescriptionVersion {
    id: string;
    prescriptionId: string;
    versionNumber: number;
    changesSummary?: string;
    data: Record<string, unknown>;
    modifiedById: string;
    modifiedAt: string;
    modifiedByName?: string;
}

export interface PatientMedicalConsent {
    id: string;
    patientId: string;
    organizationId?: string;
    consentType?: string;
    grantedAt: string;
    revokedAt?: string;
    documentUrl?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientAppointment {
    id: string;
    scheduledAt: string;
    status: string;
    reason?: string;
    durationMinutes?: number;
    notes?: string;
    professionalId?: string;
    professionalName?: string;
}

export interface BloodGlucoseMeasurement {
    id: string;
    value: number;
    unit?: string;
    context?: string;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface BloodPressureMeasurement {
    id: string;
    systolic: number;
    diastolic: number;
    pulse?: number;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface HbA1cMeasurement {
    id: string;
    valuePercent: number;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface WeightMeasurement {
    id: string;
    valueKg: number;
    bmi?: number;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface PhysicalActivityMeasurement {
    id: string;
    durationMinutes: number;
    activityType?: string;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export type InsulinType =
    | 'RAPID_ACTING'
    | 'SHORT_ACTING'
    | 'INTERMEDIATE_ACTING'
    | 'LONG_ACTING'
    | 'MIXED'
    | 'OTHER';

export type InsulinInjectionSite = 'ABDOMEN' | 'THIGH' | 'UPPER_ARM' | 'BUTTOCK' | 'OTHER';

export type InjectionStatus = 'TAKEN' | 'SKIPPED' | 'DELAYED';

export interface Insulin {
    id: string;
    medicationId: string;
    medicationName?: string;
    insulinType: InsulinType;
    concentration?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface InsulinInjection {
    id: string;
    patientId?: string;
    prescriptionItemId: string;
    insulinId: string;
    injectedAt: string;
    doseUnits: string;
    injectionSite?: InsulinInjectionSite;
    status?: InjectionStatus;
    issuerId?: string;
    issuerName?: string;
    notes?: string;
    createdAt: string;
    updatedAt?: string;
}

export interface LaboratoryResult {
    id: string;
    testName: string;
    labName?: string;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientMeal {
    id: string;
    name: string;
    description?: string;
    mealType?: string;
    measuredAt?: string;
    createdAt?: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientMealItem {
    id: string;
    mealId: string;
    foodId: string;
    portionGrams: string;
    breadUnits?: string;
    createdAt: string;
    createdById?: string;
    createdByName?: string;
}

export interface PatientDossierData {
    profile: PatientProfile;
    record: MedicalRecord | null;
    allergies: PatientAllergy[];
    diagnoses: PatientDiagnosis[];
    emergencyContacts: PatientEmergencyContact[];
    consents: PatientMedicalConsent[];
    notes: PatientMedicalNote[];
    prescriptions: PatientPrescription[];
    prescriptionItems: PrescriptionItem[];
    prescriptionVersions: PrescriptionVersion[];
    appointments: PatientAppointment[];
    meals: PatientMeal[];
    mealItems: PatientMealItem[];
    measurements: {
        bloodGlucose: BloodGlucoseMeasurement[];
        bloodPressure: BloodPressureMeasurement[];
        hba1c: HbA1cMeasurement[];
        weight: WeightMeasurement[];
        physicalActivity: PhysicalActivityMeasurement[];
        laboratoryResults: LaboratoryResult[];
        insulinInjections: InsulinInjection[];
    };
}

export interface PatientMedicalConsent {
    id: string;
    patientId: string;
    organizationId?: string;
    consentType?: string;
    grantedAt: string;
    revokedAt?: string;
    documentUrl?: string;
}

export type DossierTabId =
    | 'overview'
    | 'medical-profile'
    | 'measurements'
    | 'prescriptions'
    | 'consultations'
    | 'nutrition'
    | 'appointments'
    | 'notes'
    | 'communications';

export type MeasurementPeriod = '7d' | '30d' | '90d' | 'all';

export type MeasurementTypeId =
    | 'bloodGlucose'
    | 'bloodPressure'
    | 'hba1c'
    | 'weight'
    | 'physicalActivity'
    | 'laboratory'
    | 'insulinInjection';

export interface PrescriptionPayload {
    patientId: string;
    prescriberId: string;
    organizationId: string;
    startDate: string;
    endDate?: string;
    status: 'ACTIVE' | 'DRAFT';
    notes?: string;
}

export interface PrescriptionItemPayload {
    prescriptionId: string;
    medicationId: string;
    dosage: string;
    quantity: string;
    morning: boolean;
    noon: boolean;
    evening: boolean;
    instructions?: string;
}

export interface PrescriptionVersionPayload {
    prescriptionId: string;
    versionNumber: number;
    changesSummary?: string;
    data: Record<string, unknown>;
    modifiedById: string;
    modifiedAt: string;
}
