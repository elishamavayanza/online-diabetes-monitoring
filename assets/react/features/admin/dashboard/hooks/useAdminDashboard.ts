import { useApiQuery } from '@/react/hooks/useApiQuery';
import { fetchAdminDashboardData } from '../services/adminDashboardService';
import { AdminDashboardData } from '../types';

export function useAdminDashboard() {
    return useApiQuery<AdminDashboardData | null>(
        () => fetchAdminDashboardData(),
        { fallbackMessage: 'Impossible de charger le tableau de bord.' },
    );
}