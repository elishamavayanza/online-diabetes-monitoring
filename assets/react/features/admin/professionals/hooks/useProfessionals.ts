import { useEffect, useState } from 'react';
import { fetchProfessionals } from '../services/professionalsService';
import { Professional } from '../types';

export function useProfessionals() {
    const [professionals, setProfessionals] = useState<Professional[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchProfessionals();
                setProfessionals(data);
            } catch (err) {
                setError('Impossible de charger les professionnels.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { professionals, isLoading, error };
}
