import { useEffect, useState } from 'react';
import { fetchUsers } from '../services/usersService';
import { User } from '../types';

export type UserFilterTab = 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs';

export function useUsers(initialFilter: UserFilterTab = 'Tous') {
    const [users, setUsers] = useState<User[]>([]);
    const [filter, setFilter] = useState<UserFilterTab>(initialFilter);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchUsers(filter);
                setUsers(data);
            } catch (err) {
                setError('Impossible de charger les utilisateurs.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [filter]);

    return { users, filter, setFilter, isLoading, error };
}
