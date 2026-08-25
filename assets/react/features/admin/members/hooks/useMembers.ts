import { useEffect, useState } from 'react';
import { fetchMembers } from '../services/membersService';
import { Member } from '../types';

export function useMembers() {
    const [members, setMembers] = useState<Member[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchMembers();
                setMembers(data);
            } catch (err) {
                setError('Impossible de charger les membres.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { members, isLoading, error };
}
