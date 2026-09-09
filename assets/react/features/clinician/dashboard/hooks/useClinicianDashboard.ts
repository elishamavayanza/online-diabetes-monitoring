import { useApiQuery } from '@/react/hooks/useApiQuery';
import { toErrorState } from '@/services/api/errorDisplay';
import { fetchClinicianDashboardData } from '../services/clinicianDashboardService';
import { ClinicianDashboardData } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useClinicianDashboard() {
    const { showToast } = useToast();

    return useApiQuery<ClinicianDashboardData | null>(
        () => fetchClinicianDashboardData(),
        {
            fallbackMessage: 'Impossible de charger le tableau de bord.',
            onError: (err) => {
                showToast({
                    type: 'error',
                    message: toErrorState(err, { fallbackMessage: 'Impossible de charger le tableau de bord.' }).message,
                });
            },
        },
    );
}