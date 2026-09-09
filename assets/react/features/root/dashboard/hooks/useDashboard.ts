import { useApiQuery } from '@/react/hooks/useApiQuery';
import { fetchDashboardData } from '../services/dashboardService';
import { DashboardData } from '../types';

export function useDashboard() {
    return useApiQuery<DashboardData | null>(
        () => fetchDashboardData(),
        { fallbackMessage: 'Impossible de charger les données du tableau de bord.' },
    );
}