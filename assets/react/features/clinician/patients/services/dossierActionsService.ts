import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { MeasurementTypeId } from '../types';

export async function createBloodGlucose(patientId: string, data: {
    value: string;
    unit: string;
    context: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/blood-glucose-measurements`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors du prélèvement glycémie.');
}

export async function createBloodPressure(patientId: string, data: {
    systolic: string;
    diastolic: string;
    pulse?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/blood-pressure-measurements`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors du prélèvement tension.');
}

export async function createHbA1c(patientId: string, data: {
    valuePercent: string;
    measuredAt?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/hba1c-measurements`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors du prélèvement HbA1c.');
}

export async function createWeight(patientId: string, data: {
    valueKg: string;
    heightCm?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/weight-measurements`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors du prélèvement poids.');
}

export async function createPhysicalActivity(patientId: string, data: {
    activityType: string;
    durationMinutes: number;
    caloriesBurned?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/physical-activity-measurements`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors de l\'enregistrement activité.');
}

export async function createLaboratoryResult(patientId: string, data: {
    testName: string;
    labName?: string;
    fileUrl?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/patients/${patientId}/laboratory-results`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors de l\'enregistrement laboratoire.');
}

export async function createMeasurement(
    patientId: string,
    type: MeasurementTypeId,
    payload: Record<string, unknown>,
) {
    switch (type) {
        case 'bloodGlucose':
            return createBloodGlucose(patientId, payload as { value: string; unit: string; context: string });
        case 'bloodPressure':
            return createBloodPressure(patientId, payload as { systolic: string; diastolic: string; pulse?: string });
        case 'hba1c':
            return createHbA1c(patientId, payload as { valuePercent: string; measuredAt?: string });
        case 'weight':
            return createWeight(patientId, payload as { valueKg: string; heightCm?: string });
        case 'physicalActivity':
            return createPhysicalActivity(patientId, payload as { activityType: string; durationMinutes: number });
        case 'laboratory':
            return createLaboratoryResult(patientId, payload as { testName: string; labName?: string });
        default:
            throw new Error('Type de mesure inconnu.');
    }
}

export async function createAppointment(data: {
    patientId: string;
    professionalId: string;
    organizationId: string;
    scheduledAt: string;
    durationMinutes: number;
    status: string;
    reason?: string;
    notes?: string;
    facilityId?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/appointments', data);
    return unwrapApiData(response.data, 'Erreur lors de la création du rendez-vous.');
}

export async function createPrescription(data: {
    patientId: string;
    prescriberId: string;
    organizationId: string;
    startDate: string;
    endDate?: string;
    status: string;
    notes?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/prescriptions', data);
    return unwrapApiData(response.data, 'Erreur lors de la création de la prescription.');
}

export async function createMedicalNote(data: {
    medicalRecordId: string;
    authorId: string;
    content: string;
    notedAt: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/medical-notes', data);
    return unwrapApiData(response.data, 'Erreur lors de la création de la note.');
}

export async function createMeal(data: {
    name: string;
    mealType: string;
    description?: string;
    patientId: number | string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/meals', data);
    return unwrapApiData(response.data, 'Erreur lors de l\'enregistrement du repas.');
}

// --- Allergies ---

export async function createAllergy(data: {
    patientId: string;
    name: string;
    severity: string;
    reaction?: string;
    notes?: string;
    diagnosedAt: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/allergies', data);
    return unwrapApiData(response.data, 'Erreur lors de la création de l\'allergie.');
}

export async function updateAllergy(id: string, data: {
    patientId: string;
    name: string;
    severity: string;
    reaction?: string;
    notes?: string;
    diagnosedAt: string;
}) {
    const response = await apiClient.put<ApiFeedback<unknown>>(`/allergies/${id}`, data);
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour de l\'allergie.');
}

export async function deleteAllergy(id: string) {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/allergies/${id}`);
    return unwrapApiData(response.data, 'Erreur lors de la suppression de l\'allergie.');
}

// --- Diagnoses ---

export async function createDiagnosis(data: {
    patientId: string;
    doctorId: string;
    conditionName: string;
    description?: string;
    diagnosedAt: string;
    status: string;
    medicalRecordId?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/diagnoses', data);
    return unwrapApiData(response.data, 'Erreur lors de la création du diagnostic.');
}

export async function updateDiagnosis(id: string, data: {
    patientId: string;
    doctorId: string;
    conditionName: string;
    description?: string;
    diagnosedAt: string;
    status: string;
    medicalRecordId?: string;
}) {
    const response = await apiClient.put<ApiFeedback<unknown>>(`/diagnoses/${id}`, data);
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour du diagnostic.');
}

export async function deleteDiagnosis(id: string) {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/diagnoses/${id}`);
    return unwrapApiData(response.data, 'Erreur lors de la suppression du diagnostic.');
}

// --- Medical consents ---

export async function createMedicalConsent(data: {
    patientId: string;
    organizationId?: string;
    consentType: string;
    grantedAt: string;
    revokedAt?: string;
    documentUrl?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/medical-consents', data);
    return unwrapApiData(response.data, 'Erreur lors de l\'enregistrement du consentement.');
}

export async function updateMedicalConsent(id: string, data: {
    patientId: string;
    organizationId?: string;
    consentType: string;
    grantedAt: string;
    revokedAt?: string;
    documentUrl?: string;
}) {
    const response = await apiClient.put<ApiFeedback<unknown>>(`/medical-consents/${id}`, data);
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour du consentement.');
}

export async function deleteMedicalConsent(id: string) {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/medical-consents/${id}`);
    return unwrapApiData(response.data, 'Erreur lors de la suppression du consentement.');
}

// --- Emergency contacts ---

export async function createEmergencyContact(data: {
    patientId: string;
    fullName: string;
    relationship: string;
    phone: string;
    email?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/emergency-contacts', data);
    return unwrapApiData(response.data, 'Erreur lors de la création du contact.');
}

export async function updateEmergencyContact(id: string, data: {
    patientId: string;
    fullName: string;
    relationship: string;
    phone: string;
    email?: string;
}) {
    const response = await apiClient.put<ApiFeedback<unknown>>(`/emergency-contacts/${id}`, data);
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour du contact.');
}

export async function deleteEmergencyContact(id: string) {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/emergency-contacts/${id}`);
    return unwrapApiData(response.data, 'Erreur lors de la suppression du contact.');
}

// --- Prescription items & versions ---

export async function createPrescriptionItem(data: {
    prescriptionId: string;
    medicationId: string;
    dosage: string;
    quantity: string;
    morning: boolean;
    noon: boolean;
    evening: boolean;
    instructions?: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/prescription-items', data);
    return unwrapApiData(response.data, 'Erreur lors de l\'ajout du médicament.');
}

export async function deletePrescriptionItem(id: string) {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/prescription-items/${id}`);
    return unwrapApiData(response.data, 'Erreur lors de la suppression du médicament.');
}

export async function createPrescriptionVersion(data: {
    prescriptionId: string;
    versionNumber: number;
    changesSummary?: string;
    data: Record<string, unknown>;
    modifiedById: string;
    modifiedAt: string;
}) {
    const response = await apiClient.post<ApiFeedback<unknown>>('/prescription-versions', data);
    return unwrapApiData(response.data, 'Erreur lors de la création de la version.');
}

