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
