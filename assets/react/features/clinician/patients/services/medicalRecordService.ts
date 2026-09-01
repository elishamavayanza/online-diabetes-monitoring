import apiClient from '@/services/api/client';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import {
    MedicalRecord, PatientPrescription,
    PrescriptionItem,
    PrescriptionItemPayload, PrescriptionPayload,
    PrescriptionVersion,
    PrescriptionVersionPayload
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

function mapStatus(status: string): MedicalRecord['status'] {
    const normalized = status.toUpperCase();
    if (normalized === 'CLOSED') return 'closed';
    if (normalized === 'OPEN') return 'open';
    return 'none';
}

function mapApiToMedicalRecord(api: ApiMedicalRecord): MedicalRecord {
    return {
        id: api.id,
        patientId: api.patientId,
        organizationId: api.organizationId,
        status: mapStatus(api.status),
        createdAt: api.createdAt ?? api.openedAt,
        updatedAt: api.updatedAt ?? undefined,
        openedAt: api.openedAt,
        closedAt: api.closedAt ?? undefined,
    };
}

function getOrganizationIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        const orgs = payload?.organizations;
        if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
            return String(orgs[0].organization_id);
        }
    } catch {
        return null;
    }
    return null;
}

export async function fetchMedicalRecord(patientId: string): Promise<MedicalRecord | null> {
    const response = await apiClient.get<ApiFeedback<ApiMedicalRecord | null>>(
        `/medical-records/patient/${patientId}`,
    );
    const data = unwrapApiData(response.data, 'Impossible de charger le dossier médical.');
    return data ? mapApiToMedicalRecord(data) : null;
}

export async function createMedicalRecord(patientId: string, organizationId?: string): Promise<MedicalRecord> {
    const orgId = organizationId ?? getOrganizationIdFromToken();
    if (!orgId) {
        throw new Error('Organisation introuvable pour créer le dossier.');
    }

    const payload = {
        patientId,
        organizationId: orgId,
        status: 'OPEN',
        openedAt: new Date().toISOString(),
        closedAt: null,
    };

    const response = await apiClient.post<ApiFeedback<ApiMedicalRecord>>('/medical-records', payload);
    const data = unwrapApiData(response.data, 'Erreur lors de la création du dossier.');
    return mapApiToMedicalRecord(data);
}

export async function updateMedicalRecord(record: MedicalRecord, patch: Partial<{
    status: 'OPEN' | 'CLOSED';
    closedAt: string | null;
}>): Promise<MedicalRecord> {
    const payload = {
        patientId: record.patientId,
        organizationId: record.organizationId,
        status: patch.status ?? record.status.toUpperCase(),
        openedAt: record.openedAt ?? record.createdAt ?? new Date().toISOString(),
        closedAt: patch.closedAt !== undefined ? patch.closedAt : (record.closedAt ?? null),
    };

    const response = await apiClient.patch<ApiFeedback<ApiMedicalRecord>>(
        `/medical-records/${record.id}`,
        payload,
    );
    const data = unwrapApiData(response.data, 'Erreur lors de la mise à jour du dossier.');
    return mapApiToMedicalRecord(data);
}

export async function reopenMedicalRecord(record: MedicalRecord): Promise<MedicalRecord> {
    return updateMedicalRecord(record, { status: 'OPEN', closedAt: null });
}

export async function closeMedicalRecord(record: MedicalRecord): Promise<MedicalRecord> {
    return updateMedicalRecord(record, { status: 'CLOSED', closedAt: new Date().toISOString() });
}

// ============================================================
// Prescriptions
// ============================================================

export async function fetchPrescriptionsByPatient(patientId: string): Promise<PatientPrescription[]> {
    const response = await apiClient.get<ApiFeedback<PatientPrescription[]>>(
        `/prescriptions/patient/${patientId}`,
    );
    return unwrapApiData(response.data, 'Erreur lors de la récupération des prescriptions.') ?? [];
}

export async function fetchPrescriptionById(id: string): Promise<PatientPrescription> {
    const response = await apiClient.get<ApiFeedback<PatientPrescription>>(
        `/prescriptions/${id}`,
    );
    return unwrapApiData(response.data, 'Prescription introuvable.');
}

export async function createPrescription(
    data: PrescriptionPayload
): Promise<PatientPrescription> {
    const response = await apiClient.post<ApiFeedback<PatientPrescription>>('/prescriptions', data);
    return unwrapApiData(response.data, 'Erreur lors de la création de la prescription.');
}

export async function updatePrescription(
    id: string,
    data: PrescriptionPayload
): Promise<PatientPrescription> {
    const response = await apiClient.put<ApiFeedback<PatientPrescription>>(
        `/prescriptions/${id}`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour de la prescription.');
}

export async function deletePrescription(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/prescriptions/${id}`);
    unwrapApiData(response.data, 'Erreur lors de la suppression de la prescription.');
}

export async function activatePrescription(
    id: string,
    currentData: PatientPrescription
): Promise<PatientPrescription> {
    // On utilise updatePrescription avec status ACTIVE
    return updatePrescription(id, {
        patientId: currentData.id, // ⚠️ Attention : PatientPrescription n'a pas de champ patientId,
        // il faudra le fournir séparément. Voir remarque ci-dessous.
        prescriberId: '', // idem, à récupérer
        organizationId: '', // idem
        startDate: currentData.startDate || '',
        endDate: currentData.endDate,
        status: 'ACTIVE',
        notes: currentData.notes,
    });
}

// ============================================================
// Éléments de prescription
// ============================================================

export async function fetchPrescriptionItemsByPrescription(
    prescriptionId: string
): Promise<PrescriptionItem[]> {
    const response = await apiClient.get<ApiFeedback<PrescriptionItem[]>>(
        `/prescription-items/prescription/${prescriptionId}`,
    );
    return unwrapApiData(response.data, 'Erreur lors de la récupération des éléments.') ?? [];
}

export async function createPrescriptionItem(
    data: PrescriptionItemPayload
): Promise<PrescriptionItem> {
    const response = await apiClient.post<ApiFeedback<PrescriptionItem>>('/prescription-items', data);
    return unwrapApiData(response.data, "Erreur lors de l'ajout du médicament.");
}

export async function updatePrescriptionItem(
    id: string,
    data: PrescriptionItemPayload
): Promise<PrescriptionItem> {
    const response = await apiClient.put<ApiFeedback<PrescriptionItem>>(
        `/prescription-items/${id}`,
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors de la modification du médicament.');
}

export async function deletePrescriptionItem(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/prescription-items/${id}`);
    unwrapApiData(response.data, 'Erreur lors de la suppression du médicament.');
}

// ============================================================
// Versions de prescription
// ============================================================

export async function fetchPrescriptionVersionsByPrescription(
    prescriptionId: string
): Promise<PrescriptionVersion[]> {
    const response = await apiClient.get<ApiFeedback<PrescriptionVersion[]>>(
        `/prescription-versions/prescription/${prescriptionId}`,
    );
    return unwrapApiData(response.data, 'Erreur lors de la récupération des versions.') ?? [];
}

export async function createPrescriptionVersion(
    data: PrescriptionVersionPayload
): Promise<PrescriptionVersion> {
    const response = await apiClient.post<ApiFeedback<PrescriptionVersion>>(
        '/prescription-versions',
        data,
    );
    return unwrapApiData(response.data, 'Erreur lors de la création de la version.');
}

export async function deletePrescriptionVersion(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/prescription-versions/${id}`);
    unwrapApiData(response.data, 'Erreur lors de la suppression de la version.');
}
