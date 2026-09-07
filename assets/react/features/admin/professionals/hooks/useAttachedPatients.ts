// hooks/useAttachedPatients.ts

import { useState, useEffect, useCallback } from 'react';
import {
    fetchAttachedPatients,
    updateCareTeamAssignmentStatus,
    fetchPatientsForAssignment,
} from '../services/careTeamService';
import { AttachedPatient } from '../types/types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

export function useAttachedPatients(professionalId: string) {
    const { showToast } = useToast();
    const [patients, setPatients] = useState<AttachedPatient[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const getOrganizationId = (): string | null => {
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
    };

    const loadPatients = useCallback(async () => {
        if (!professionalId) {
            setPatients([]);
            setIsLoading(false);
            return;
        }

        const organizationId = getOrganizationId();
        if (!organizationId) {
            setError('Organisation introuvable.');
            setIsLoading(false);
            return;
        }

        setIsLoading(true);
        setError(null);
        try {
            // 1. Récupérer les assignations du professionnel (sans noms)
            const assignments = await fetchAttachedPatients(professionalId, organizationId);

            // 2. Récupérer tous les patients pour construire la correspondance id -> nom
            const allPatients = await fetchPatientsForAssignment();
            const patientMap = new Map(allPatients.map(p => [p.id, p.nom]));

            // 3. Fusionner : enrichir chaque assignation avec le nom du patient
            const mapped: AttachedPatient[] = assignments.map((assignment: any) => ({
                assignmentId: String(assignment.id),
                patientId: String(assignment.patientId),
                nom: patientMap.get(String(assignment.patientId)) ?? 'Patient',
                active: assignment.active ?? true,
            }));

            setPatients(mapped);
        } catch (err) {
            console.error('Erreur chargement patients attachés:', err);
            setError('Impossible de charger les patients attachés.');
        } finally {
            setIsLoading(false);
        }
    }, [professionalId]);

    useEffect(() => {
        loadPatients();
    }, [loadPatients]);

    const toggleActive = async (assignmentId: string, currentActive: boolean) => {
        const organizationId = getOrganizationId();
        if (!organizationId) {
            showToast({ type: 'error', message: 'Organisation introuvable.' });
            return;
        }

        // Optimistic update
        setPatients((prev) =>
            prev.map((p) =>
                p.assignmentId === assignmentId ? { ...p, active: !currentActive } : p
            )
        );

        try {
            await updateCareTeamAssignmentStatus(organizationId, assignmentId, !currentActive);
            showToast({
                type: 'success',
                message: `Patient ${!currentActive ? 'activé' : 'désactivé'} avec succès.`,
            });
        } catch (err) {
            // Rollback
            setPatients((prev) =>
                prev.map((p) =>
                    p.assignmentId === assignmentId ? { ...p, active: currentActive } : p
                )
            );
            showToast({
                type: 'error',
                message: err instanceof Error ? err.message : 'Erreur lors de la mise à jour.',
            });
        }
    };

    return { patients, isLoading, error, toggleActive, reload: loadPatients };
}
