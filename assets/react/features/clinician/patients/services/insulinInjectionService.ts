import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { Insulin, InsulinInjection, PrescriptionItem } from '../types';

export interface InsulinInjectionPayload {
    prescriptionItemId: string;
    insulinId: string;
    injectedAt: string;
    doseUnits: string;
    injectionSite: string;
    status: string;
    notes?: string;
}

interface PatientPrescriptionLite {
    id: string;
}

export async function fetchInsulins(): Promise<Insulin[]> {
    const response = await apiClient.get<ApiFeedback<Insulin[]>>('/insulins');
    return unwrapApiData(response.data, 'Erreur lors du chargement des insulines.') ?? [];
}

export async function fetchInsulinInjectionHistory(patientId: string): Promise<InsulinInjection[]> {
    const response = await apiClient.get<ApiFeedback<InsulinInjection[]>>(
        `/insulin-injections/patient/${patientId}`,
    );
    return unwrapApiData(response.data, 'Erreur lors du chargement des injections.') ?? [];
}

export async function fetchPatientPrescriptionItems(patientId: string): Promise<PrescriptionItem[]> {
    const response = await apiClient.get<ApiFeedback<PatientPrescriptionLite[]>>(
        `/prescriptions/patient/${patientId}`,
    );
    const prescriptions = unwrapApiData(response.data, 'Erreur lors du chargement des prescriptions.') ?? [];
    const lists = await Promise.all(
        prescriptions.map((rx) =>
            apiClient.get<ApiFeedback<PrescriptionItem[]>>(`/prescription-items/prescription/${rx.id}`),
        ),
    );
    return lists.flatMap(
        (res) => unwrapApiData(res.data, 'Erreur lors du chargement des médicaments prescrits.') ?? [],
    );
}

export async function createInsulinInjection(payload: InsulinInjectionPayload): Promise<InsulinInjection> {
    const response = await apiClient.post<ApiFeedback<InsulinInjection>>('/insulin-injections', payload);
    return unwrapApiData(response.data, 'Erreur lors de l\'enregistrement de l\'injection d\'insuline.');
}