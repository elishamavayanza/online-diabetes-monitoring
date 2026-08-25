import { useEffect, useState } from 'react';
import { fetchNutritionistPatients } from '../services/nutritionistPatientsService';
import { NutritionistPatient } from '../types';

export function useNutritionistPatients() {
    const [patients, setPatients] = useState<NutritionistPatient[]>([]);
    const [search, setSearch] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchNutritionistPatients(search);
                setPatients(data);
            } catch (err) {
                setError('Impossible de charger les patients.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [search]);

    return { patients, search, setSearch, isLoading, error };
}
