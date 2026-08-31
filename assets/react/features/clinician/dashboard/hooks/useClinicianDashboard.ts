import { useEffect, useState } from 'react';
import { fetchClinicianDashboardData } from '../services/clinicianDashboardService';
import { ClinicianDashboardData } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useClinicianDashboard() {
    const [data, setData] = useState<ClinicianDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const result = await fetchClinicianDashboardData();
                setData(result);
            } catch (err) {
                const message = err instanceof Error ? err.message : 'Impossible de charger le tableau de bord.';
                setError(message);
                showToast({ type: 'error', message });
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [showToast]);

    return { data, isLoading, error };
}
