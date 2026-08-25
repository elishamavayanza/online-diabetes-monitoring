import { useEffect, useState } from 'react';
import { fetchDoses } from '../services/dosesService';
import { MedicationIntake } from '../types';

export function useDoses() {
    const [intakes, setIntakes] = useState<MedicationIntake[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchDoses();
                setIntakes(data.today);
            } catch (err) {
                setError('Impossible de charger les prises.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { intakes, isLoading, error };
}
