import { useEffect, useState } from 'react';
import { fetchClinicianDashboardData } from '../services/clinicianDashboardService';
import { ClinicianDashboardData } from '../types';

export function useClinicianDashboard() {
    const [data, setData] = useState<ClinicianDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchClinicianDashboardData();
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
