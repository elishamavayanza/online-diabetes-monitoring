import apiClient from "@/services/api/client";
import { Medication, MedicationFormValues, MedicationFilters } from '../types/types';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

// Inutile maintenant car le backend utilise l'organisation du token
function getOrganizationIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        const orgs = payload?.organizations;
        if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
            return String(orgs[0].organization_id);
        }
    } catch (e) {
        console.error('Erreur décodage token:', e);
    }
    return null;
}

/**
 * Récupère la liste des médicaments (sans paramètres, le backend ne les supporte pas).
 */
export async function fetchMedications(): Promise<Medication[]> {
    try {
        const response = await apiClient.get<ApiFeedback<Medication[]>>('/medications');
        if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la récupération des médicaments');
        return response.data.data ?? [];
    } catch (error) {
        console.error('Erreur fetchMedications:', error);
        throw error;
    }
}

/**
 * Crée un médicament.
 */
export async function createMedication(payload: MedicationFormValues): Promise<Medication> {
    try {
        const response = await apiClient.post<ApiFeedback<Medication>>('/medications', payload);
        if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la création du médicament');
        return response.data.data;
    } catch (error) {
        console.error('Erreur createMedication:', error);
        throw error;
    }
}

/**
 * Met à jour un médicament.
 */
export async function updateMedication(id: string, payload: MedicationFormValues): Promise<Medication> {
    try {
        const response = await apiClient.put<ApiFeedback<Medication>>(
            `/medications/${id}`,
            payload
        );
        if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la mise à jour du médicament');
        return response.data.data;
    } catch (error) {
        console.error('Erreur updateMedication:', error);
        throw error;
    }
}

/**
 * Supprime un médicament.
 */
export async function deleteMedication(id: string): Promise<void> {
    try {
        const response = await apiClient.delete<ApiFeedback<unknown>>(`/medications/${id}`);
        if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la suppression du médicament');
    } catch (error) {
        console.error('Erreur deleteMedication:', error);
        throw error;
    }
}
