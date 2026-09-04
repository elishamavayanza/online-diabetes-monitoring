// hooks/useCareTeam.ts
import { useEffect, useState } from 'react';
import { fetchCareTeam } from '../services/teamService';
import { CareTeamMember } from '../types';
import {useAuth} from "@/react/app/providers/AuthProvider";

export function useCareTeam() {
    const { user } = useAuth();
    const patientId = user?.id; // l'ID du patient connecté
    const [members, setMembers] = useState<CareTeamMember[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!patientId) {
            setError('Utilisateur non identifié.');
            setIsLoading(false);
            return;
        }

        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchCareTeam(patientId);
                setMembers(data);
            } catch (err) {
                setError('Impossible de charger votre équipe.');
            } finally {
                setIsLoading(false);
            }
        };

        load();
    }, [patientId]);

    return { members, isLoading, error };
}
