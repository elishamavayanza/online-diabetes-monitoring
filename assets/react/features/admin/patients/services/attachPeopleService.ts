import apiClient from "@/services/api/client";
import {
    AttachPeopleFormValues,
    CareTeamAssignmentItem,
    ProfessionalOption,
} from '../types/attachPeople.types';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

/**
 * Récupère l'ID de l'organisation depuis le token JWT.
 */
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
 * Attache plusieurs professionnels à un patient.
 * Pour chaque affectation, appelle POST /care-team-assignments.
 */
export async function attachProfessionalsToPatient(
    payload: AttachPeopleFormValues
): Promise<void> {
    const organizationId = getOrganizationIdFromToken();
    if (!organizationId) throw new Error('Organisation introuvable');

    try {
        for (const assignment of payload.assignments) {
            if (!assignment.professionalId || !assignment.startDate) continue; // ignorer affectations incomplètes
            const response = await apiClient.post<ApiFeedback<unknown>>(
                `/healthcare-organizations/${organizationId}/care-team-assignments`,
                {
                    patientId: Number(payload.patientId),
                    professionalId: Number(assignment.professionalId),
                    role: assignment.role,
                    startDate: assignment.startDate,
                    endDate: assignment.endDate || null,
                    active: assignment.active,
                }
            );
            if (response.data.error) throw new Error(response.data.message || "Erreur lors de l'affectation");
        }
    } catch (error) {
        console.error('Erreur attachProfessionalsToPatient:', error);
        throw error;
    }
}

/**
 * Récupère la liste des professionnels disponibles pour l'attachement.
 */
export async function fetchProfessionalsForAttach(): Promise<ProfessionalOption[]> {
    try {
        const response = await apiClient.get<ApiFeedback<any[]>>('/professionals');
        const data = response.data.data ?? [];
        return data.map((p: any) => ({
            id: String(p.id ?? ''),
            nom: p.fullName ?? '',
            specialty: p.specialty ?? '',
        }));
    } catch (error) {
        console.error('Erreur fetchProfessionalsForAttach:', error);
        throw error;
    }
}

/**
 * Récupère les affectations existantes pour un patient.
 */
export async function fetchExistingAssignments(
    patientId: string
): Promise<CareTeamAssignmentItem[]> {
    const organizationId = getOrganizationIdFromToken();
    if (!organizationId) throw new Error('Organisation introuvable');

    try {
        const response = await apiClient.get<ApiFeedback<any[]>>(
            `/healthcare-organizations/${organizationId}/care-team-assignments`
        );
        const data = response.data.data ?? [];
        // Filtrer les affectations du patient
        const patientAssignments = data.filter((a: any) => String(a.patientId) === String(patientId));
        return patientAssignments.map((a: any) => ({
            id: String(a.id ?? ''),
            professionalId: String(a.professionalId ?? ''),
            role: a.role,
            startDate: a.startDate?.slice(0, 10) ?? '',
            endDate: a.endDate?.slice(0, 10) ?? '',
            active: a.active,
        }));
    } catch (error) {
        console.error('Erreur fetchExistingAssignments:', error);
        throw error;
    }
}

/**
 * Met à jour les affectations d'un patient.
 * On ne peut pas modifier en masse ; on peut supprimer/ajouter individuellement.
 * Pour simplifier, on supprime toutes les affectations existantes puis on recrée.
 */
export async function updateAssignmentsForPatient(
    patientId: string,
    payload: AttachPeopleFormValues
): Promise<void> {
    const organizationId = getOrganizationIdFromToken();
    if (!organizationId) throw new Error('Organisation introuvable');

    try {
        // 1. Récupérer les affectations existantes
        const existing = await fetchExistingAssignments(patientId);

        // 2. Supprimer chaque affectation existante
        for (const assignment of existing) {
            await apiClient.delete(
                `/healthcare-organizations/${organizationId}/care-team-assignments/${assignment.id}`
            );
        }

        // 3. Recréer les affectations fournies
        await attachProfessionalsToPatient(payload);
    } catch (error) {
        console.error('Erreur updateAssignmentsForPatient:', error);
        throw error;
    }
}
