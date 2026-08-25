import { NutritionistDashboardData } from '../types';

export async function fetchNutritionistDashboardData(): Promise<NutritionistDashboardData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        stats: [
            { id: 'patients', label: 'Mes patients', value: 32 },
            { id: 'appointments-today', label: 'Rendez-vous aujourd\'hui', value: 5 },
            { id: 'plans', label: 'Plans alimentaires actifs', value: 18 },
            { id: 'follow-up-needed', label: 'Patients à suivre', value: 4 },
        ],
        appointmentsToday: [
            { id: '1', time: '08:30', patient: 'Marie Zawadi' },
            { id: '2', time: '10:00', patient: 'Jean-Pierre L.' },
            { id: '3', time: '11:30', patient: 'Alice M.' },
        ],
        recentActivities: [
            { id: '1', message: 'Plan alimentaire modifié', timestamp: 'Il y a 15 min' },
            { id: '2', message: 'Nouveau patient affecté', timestamp: 'Il y a 1 h' },
            { id: '3', message: 'Rendez-vous confirmé', timestamp: 'Il y a 2 h' },
        ],
    };
}
