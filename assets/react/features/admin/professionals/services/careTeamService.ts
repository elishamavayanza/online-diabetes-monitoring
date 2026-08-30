// services/careTeamService.ts
import apiClient from "@/services/api/client";
import { CareTeamAssignmentFormValues } from '../types/types';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

/**
 * Affecte un patient à un professionnel.
 * @param organizationId ID de l'organisation
 * @param payload Données de l'affectation (patientId, professionalId, role, dates, active)
 */
export async function assignPatientToProfessional(
    organizationId: string,
    payload: CareTeamAssignmentFormValues
): Promise<void> {
    try {
        const response = await apiClient.post<ApiFeedback<unknown>>(
            `/healthcare-organizations/${organizationId}/care-team-assignments`,
            payload
        );
        if (response.data.error) {
            console.error('Réponse erreur affectation:', response.data);
            // Extraire les détails si disponibles
            const errors = (response.data as any).errors;
            const errorMessage = errors
                ? Object.values(errors).flat().join(', ')
                : response.data.message || "Erreur lors de l'affectation";
            throw new Error(errorMessage);
        }
    } catch (error) {
        console.error("Erreur assignPatientToProfessional:", error);
        throw error;
    }
}

/**
 * Récupère la liste des patients pour l'affectation.
 * Utilise l'endpoint GET /api/patients (disponible).
 */
export async function fetchPatientsForAssignment(): Promise<{ id: string; nom: string }[]> {
    try {
        const response = await apiClient.get<ApiFeedback<any[]>>('/patients');
        const data = response.data.data ?? [];
        return data.map((p: any) => ({
            id: String(p.id ?? ''),
            nom: p.fullName ?? p.name ?? `${p.firstName ?? ''} ${p.lastName ?? ''}`.trim(),
        }));
    } catch (error) {
        console.error("Erreur fetchPatientsForAssignment:", error);
        throw error;
    }
}
