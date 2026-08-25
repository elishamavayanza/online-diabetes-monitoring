import { useEffect, useState } from 'react';
import { fetchNutritionistDashboardData } from '../services/nutritionistDashboardService';
import { NutritionistDashboardData } from '../types';

export function useNutritionistDashboard() {
    const [data, setData] = useState<NutritionistDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchNutritionistDashboardData();
                setData(result);
            } catch (err) {
                setError('Impossible de charger le tableau de bord.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
