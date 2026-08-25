import { useEffect, useState } from 'react';
import { fetchDashboardData } from '../services/dashboardService';
import { DashboardData } from '../types';

export function useDashboard() {
    const [data, setData] = useState<DashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchDashboardData();
                setData(result);
            } catch (err) {
                setError('Impossible de charger les données du tableau de bord.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
