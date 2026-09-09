import { useApiQuery } from '@/react/hooks/useApiQuery';
import { fetchNutritionistDashboardData } from '../services/nutritionistDashboardService';
import { NutritionistDashboardData } from '../types';

export function useNutritionistDashboard() {
    return useApiQuery<NutritionistDashboardData | null>(
        () => fetchNutritionistDashboardData(),
        { fallbackMessage: 'Impossible de charger le tableau de bord.' },
    );
}