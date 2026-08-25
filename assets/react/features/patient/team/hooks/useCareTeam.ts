import { useEffect, useState } from 'react';
import { fetchCareTeam } from '../services/teamService';
import { CareTeamMember } from '../types';

export function useCareTeam() {
    const [members, setMembers] = useState<CareTeamMember[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchCareTeam();
                setMembers(data);
            } catch (err) {
                setError('Impossible de charger votre équipe.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { members, isLoading, error };
}
