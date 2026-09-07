// services/careTeamService.ts
import apiClient from "@/services/api/client";
import {AttachedPatient, CareTeamAssignmentFormValues} from '../types/types';

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
        //  Conversion des IDs en nombres pour correspondre aux types backend
        const dataToSend = {
            ...payload,
            patientId: Number(payload.patientId),
            professionalId: Number(payload.professionalId),
        };

        const response = await apiClient.post<ApiFeedback<unknown>>(
            `/healthcare-organizations/${organizationId}/care-team-assignments`,
            dataToSend
        );

        if (response.data.error) {
            console.error('Réponse erreur affectation:', response.data);
            // Extraction des erreurs détaillées si disponibles
            const errors = (response.data as any).errors;
            const errorMessage = errors
                ? Object.values(errors).flat().join(', ')
                : response.data.message || "Erreur lors de l'affectation";
            throw new Error(errorMessage);
        }
    } catch (error) {
        console.error("Erreur assignPatientToProfessional:", error);
        if (error instanceof Error) throw error;
        throw new Error("Erreur inconnue lors de l'affectation");
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

/**
 * Récupère la liste des patients attachés à un professionnel.
 * Adaptez l'URL selon votre backend (ex: /care-team-assignments?professionalId=...)
 */
// services/careTeamService.ts

export async function fetchAttachedPatients(
    professionalId: string,
    organizationId: string
): Promise<any[]> {
    try {
        const response = await apiClient.get<ApiFeedback<any[]>>(
            `/healthcare-organizations/${organizationId}/care-team-assignments`
        );
        const assignments = response.data.data ?? [];

        // Filtrer pour ce professionnel
        return assignments.filter((a: any) => String(a.professionalId) === professionalId);
    } catch (error) {
        console.error("Erreur fetchAttachedPatients:", error);
        throw error;
    }
}

/**
 * Met à jour le statut actif/inactif d'une assignation patient-professionnel.
 */
export async function updateCareTeamAssignmentStatus(
    organizationId: string,
    assignmentId: string,
    active: boolean
): Promise<void> {
    try {
        const response = await apiClient.patch<ApiFeedback<unknown>>(
            `/healthcare-organizations/${organizationId}/care-team-assignments/${assignmentId}`,
            { active }
        );
        if (response.data.error) {
            const errors = (response.data as any).errors;
            const errorMessage = errors
                ? Object.values(errors).flat().join(', ')
                : response.data.message || "Erreur lors de la mise à jour du statut.";
            throw new Error(errorMessage);
        }
    } catch (error) {
        console.error("Erreur updateCareTeamAssignmentStatus:", error);
        if (error instanceof Error) throw error;
        throw new Error("Erreur inconnue lors de la mise à jour.");
    }
}
