import { useEffect, useState } from 'react';
import { fetchAdminDashboardData } from '../services/adminDashboardService';
import { AdminDashboardData } from '../types';

export function useAdminDashboard() {
    const [data, setData] = useState<AdminDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchAdminDashboardData();
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
