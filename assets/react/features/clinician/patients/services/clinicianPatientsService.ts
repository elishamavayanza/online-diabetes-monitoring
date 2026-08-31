import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { ClinicianPatient } from '../types';
import { fetchMedicalRecord } from './medicalRecordService';

interface ApiPatient {
    id: string | number;
    fullName?: string;
    dateOfBirth?: string;
    phone?: string;
    avatarUrl?: string;
    status?: string;
    organizationId?: string;
}

function mapPatientStatus(status?: string): 'Active' | 'Inactive' {
    if (!status) return 'Active';
    return status.toUpperCase() === 'ACTIVE' || status === 'Active' ? 'Active' : 'Inactive';
}

async function mapToClinicianPatient(apiPatient: ApiPatient): Promise<ClinicianPatient> {
    const id = String(apiPatient.id);
    let medicalRecordStatus: ClinicianPatient['medicalRecordStatus'] = 'none';

    try {
        const record = await fetchMedicalRecord(id);
        if (record) {
            medicalRecordStatus = record.status === 'closed' ? 'closed' : 'open';
        }
    } catch {
        medicalRecordStatus = 'none';
    }

    return {
        id,
        nom: apiPatient.fullName ?? `Patient #${id}`,
        dateNaissance: apiPatient.dateOfBirth,
        telephone: apiPatient.phone,
        avatarUrl: apiPatient.avatarUrl,
        statut: mapPatientStatus(apiPatient.status),
        medicalRecordStatus,
        hasMedicalRecord: medicalRecordStatus !== 'none',
    };
}

export async function fetchClinicianPatients(search: string): Promise<ClinicianPatient[]> {
    const response = await apiClient.get<ApiFeedback<ApiPatient[]>>('/patients/assigned');
    const patients = unwrapApiData(response.data, 'Impossible de charger les patients.');

    const mapped = await Promise.all(patients.map(mapToClinicianPatient));

    if (!search.trim()) return mapped;
    const term = search.toLowerCase();
    return mapped.filter((p) => p.nom.toLowerCase().includes(term));
}

export async function fetchPatientProfile(patientId: string) {
    const response = await apiClient.get<ApiFeedback<ApiPatient>>(`/patients/${patientId}/profile`);
    return unwrapApiData(response.data, 'Profil patient introuvable.');
}
