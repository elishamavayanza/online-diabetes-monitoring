import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import {
    BloodGlucoseMeasurement,
    BloodPressureMeasurement,
    HbA1cMeasurement,
    InsulinInjection,
    LaboratoryResult,
    MedicalRecord,
    PatientAllergy,
    PatientAppointment,
    PatientDiagnosis,
    PatientDossierData,
    PatientEmergencyContact,
    PatientMedicalNote,
    PatientPrescription,
    PatientProfile,
    PatientMeal,
    PatientMealItem,
    PatientMedicalConsent,
    PrescriptionItem,
    PrescriptionVersion,
    PhysicalActivityMeasurement,
    WeightMeasurement,
} from '../types';

interface ApiMedicalRecord {
    id: string;
    patientId: string;
    organizationId: string;
    status: string;
    openedAt: string;
    closedAt?: string | null;
    createdAt?: string;
    updatedAt?: string | null;
}

interface AggregatedDossierPayload {
    profile: Record<string, unknown>;
    record: ApiMedicalRecord | null;
    allergies: PatientAllergy[];
    diagnoses: PatientDiagnosis[];
    emergencyContacts: PatientEmergencyContact[];
    consents: PatientMedicalConsent[];
    notes: PatientMedicalNote[];
    prescriptions: PatientPrescription[];
    prescriptionItems: PrescriptionItem[];
    prescriptionVersions: PrescriptionVersion[];
    appointments: PatientAppointment[];
    meals: (PatientMeal & { items?: PatientMealItem[] })[];
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

function mapProfile(data: Record<string, unknown>): PatientProfile {
    return {
        id: String(data.id ?? ''),
        fullName: String(data.fullName ?? ''),
        email: String(data.email ?? ''),
        phone: data.phone ? String(data.phone) : undefined,
        avatarUrl: data.avatarUrl ? String(data.avatarUrl) : undefined,
        dateOfBirth: data.dateOfBirth ? String(data.dateOfBirth) : undefined,
        bloodType: data.bloodType ? String(data.bloodType) : undefined,
        heightCm: data.heightCm != null ? Number(data.heightCm) : undefined,
        organizationId: data.organizationId ? String(data.organizationId) : undefined,
        organizationName: data.organizationName ? String(data.organizationName) : undefined,
        status: data.status ? String(data.status) : undefined,
    };
}

function mapMedicalRecord(api: ApiMedicalRecord | null): MedicalRecord | null {
    if (!api) return null;
    const normalized = String(api.status ?? '').toUpperCase();
    const status: MedicalRecord['status'] =
        normalized === 'CLOSED' ? 'closed' : normalized === 'OPEN' ? 'open' : 'none';

    return {
        id: String(api.id),
        patientId: String(api.patientId),
        organizationId: String(api.organizationId),
        status,
        createdAt: api.createdAt ?? api.openedAt,
        updatedAt: api.updatedAt ?? undefined,
        openedAt: api.openedAt,
        closedAt: api.closedAt ?? undefined,
    };
}

/**
 * Charge le dossier patient via l'endpoint agrégé (1 requête HTTP au lieu de ~18).
 */
export async function fetchPatientDossier(patientId: string): Promise<PatientDossierData> {
    const response = await apiClient.get<ApiFeedback<AggregatedDossierPayload>>(
        `/patients/${patientId}/dossier`,
    );
    const payload = unwrapApiData(response.data, 'Impossible de charger le dossier patient.');

    const meals = payload.meals ?? [];
    let mealItems = payload.mealItems ?? [];
    if (mealItems.length === 0 && meals.length > 0) {
        mealItems = meals.flatMap((meal) => (Array.isArray(meal.items) ? meal.items : []));
    }

    return {
        profile: mapProfile(payload.profile as unknown as Record<string, unknown>),
        record: mapMedicalRecord(payload.record ?? null),
        allergies: payload.allergies ?? [],
        diagnoses: payload.diagnoses ?? [],
        emergencyContacts: payload.emergencyContacts ?? [],
        consents: payload.consents ?? [],
        notes: payload.notes ?? [],
        prescriptions: payload.prescriptions ?? [],
        prescriptionItems: payload.prescriptionItems ?? [],
        prescriptionVersions: payload.prescriptionVersions ?? [],
        appointments: payload.appointments ?? [],
        meals,
        mealItems,
        measurements: {
            bloodGlucose: payload.measurements?.bloodGlucose ?? [],
            bloodPressure: payload.measurements?.bloodPressure ?? [],
            hba1c: payload.measurements?.hba1c ?? [],
            weight: payload.measurements?.weight ?? [],
            physicalActivity: payload.measurements?.physicalActivity ?? [],
            laboratoryResults: payload.measurements?.laboratoryResults ?? [],
            insulinInjections: payload.measurements?.insulinInjections ?? [],
        },
    };
}
