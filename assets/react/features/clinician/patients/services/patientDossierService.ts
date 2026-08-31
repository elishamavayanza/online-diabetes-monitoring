import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import {
    BloodGlucoseMeasurement,
    BloodPressureMeasurement,
    HbA1cMeasurement,
    LaboratoryResult,
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
import { fetchMedicalRecord } from './medicalRecordService';
import { fetchPatientProfile } from './clinicianPatientsService';

async function fetchList<T>(url: string): Promise<T[]> {
    try {
        const response = await apiClient.get<ApiFeedback<T[]>>(url);
        return unwrapApiData(response.data) ?? [];
    } catch {
        return [];
    }
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

export async function fetchPatientDossier(patientId: string): Promise<PatientDossierData> {
    const profileRaw = await fetchPatientProfile(patientId);
    const profile = mapProfile(profileRaw as unknown as Record<string, unknown>);
    const record = await fetchMedicalRecord(patientId);

    const [
        allergies,
        diagnoses,
        emergencyContacts,
        consents,
        prescriptions,
        appointments,
        bloodGlucose,
        bloodPressure,
        hba1c,
        weight,
        physicalActivity,
        laboratoryResults,
        notes,
        meals,
        mealItems,
    ] = await Promise.all([
        fetchList<PatientAllergy>(`/allergies/patient/${patientId}`),
        fetchList<PatientDiagnosis>(`/diagnoses/patient/${patientId}`),
        fetchList<PatientEmergencyContact>(`/emergency-contacts/patient/${patientId}`),
        fetchList<PatientMedicalConsent>(`/medical-consents/patient/${patientId}`),
        fetchList<PatientPrescription>(`/prescriptions/patient/${patientId}`),
        fetchList<PatientAppointment>(`/appointments/queries/patient/${patientId}`),
        fetchList<BloodGlucoseMeasurement>(`/patients/${patientId}/blood-glucose-measurements`),
        fetchList<BloodPressureMeasurement>(`/patients/${patientId}/blood-pressure-measurements`),
        fetchList<HbA1cMeasurement>(`/patients/${patientId}/hba1c-measurements`),
        fetchList<WeightMeasurement>(`/patients/${patientId}/weight-measurements`),
        fetchList<PhysicalActivityMeasurement>(`/patients/${patientId}/physical-activity-measurements`),
        fetchList<LaboratoryResult>(`/patients/${patientId}/laboratory-results`),
        record
            ? fetchList<PatientMedicalNote>(`/medical-notes/record/${record.id}`)
            : Promise.resolve([]),
        fetchList<PatientMeal>(`/meals?patientId=${patientId}`),
        fetchList<PatientMealItem>(`/meal-items/patient/${patientId}`),
    ]);

    const [prescriptionItems, prescriptionVersions] = await Promise.all([
        Promise.all(
            prescriptions.map((rx) =>
                fetchList<PrescriptionItem>(`/prescription-items/prescription/${rx.id}`),
            ),
        ).then((lists) => lists.flat()),
        Promise.all(
            prescriptions.map((rx) =>
                fetchList<PrescriptionVersion>(`/prescription-versions/prescription/${rx.id}`),
            ),
        ).then((lists) => lists.flat()),
    ]);

    return {
        profile,
        record,
        allergies,
        diagnoses,
        emergencyContacts,
        consents,
        notes,
        prescriptions,
        prescriptionItems,
        prescriptionVersions,
        appointments,
        meals,
        mealItems,
        measurements: {
            bloodGlucose,
            bloodPressure,
            hba1c,
            weight,
            physicalActivity,
            laboratoryResults,
        },
    };
}
