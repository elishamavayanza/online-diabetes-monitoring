import { useEffect, useState, useCallback } from 'react';
import { fetchProfessionals } from '../services/professionalsService';
import { Professional } from '../types/types';

export function useProfessionals() {
    const [professionals, setProfessionals] = useState<Professional[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchProfessionals();
            setProfessionals(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Impossible de charger les professionnels.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    return { professionals, isLoading, error, refetch: load };
}
