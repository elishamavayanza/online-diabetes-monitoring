import { useEffect, useState } from 'react';
import { fetchAgenda } from '../services/agendaService';
import { AgendaData } from '../types';

export function useAgenda() {
    const [data, setData] = useState<AgendaData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchAgenda();
                setData(result);
            } catch (err) {
                setError('Impossible de charger l\'agenda.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
