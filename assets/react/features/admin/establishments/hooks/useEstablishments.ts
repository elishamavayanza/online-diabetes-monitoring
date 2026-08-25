import { useEffect, useState } from 'react';
import { fetchEstablishments } from '../services/establishmentsService';
import { Establishment } from '../types';

export function useEstablishments() {
    const [establishments, setEstablishments] = useState<Establishment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchEstablishments();
                setEstablishments(data.establishments);
            } catch (err) {
                setError('Impossible de charger les établissements.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { establishments, isLoading, error };
}
