import { useEffect, useState } from 'react';
import { fetchOrganisations } from '../services/organisationsService';
import { Organisation } from '../types';

export function useOrganisations() {
    const [organisations, setOrganisations] = useState<Organisation[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchOrganisations();
                setOrganisations(data.organisations);
            } catch (err) {
                setError('Impossible de charger les organisations.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { organisations, isLoading, error };
}
