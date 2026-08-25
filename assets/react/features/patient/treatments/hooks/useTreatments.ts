import { useEffect, useState } from 'react';
import { fetchTreatments } from '../services/treatmentsService';
import { Treatment } from '../types';

export function useTreatments() {
    const [treatments, setTreatments] = useState<Treatment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchTreatments();
                setTreatments(data.treatments);
            } catch (err) {
                setError('Impossible de charger les traitements.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { treatments, isLoading, error };
}
