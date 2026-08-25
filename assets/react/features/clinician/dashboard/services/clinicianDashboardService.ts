import { ClinicianDashboardData } from '../types';

export async function fetchClinicianDashboardData(): Promise<ClinicianDashboardData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        stats: [
            { id: 'patients', label: 'Mes patients', value: 48 },
            { id: 'appointments-today', label: 'Rendez-vous aujourd\'hui', value: 7 },
            { id: 'appointments-upcoming', label: 'Rendez-vous à venir', value: 15 },
            { id: 'follow-up-needed', label: 'Patients nécessitant un suivi', value: 5 },
        ],
        appointmentsToday: [
            { id: '1', time: '08:00', patient: 'Patient A' },
            { id: '2', time: '09:00', patient: 'Patient B' },
            { id: '3', time: '10:30', patient: 'Patient C' },
        ],
        recentActivities: [
            { id: '1', message: 'Nouveau rendez-vous', timestamp: 'Il y a 10 min' },
            { id: '2', message: 'Nouveau message', timestamp: 'Il y a 30 min' },
            { id: '3', message: 'Patient ajouté à votre équipe', timestamp: 'Il y a 1 h' },
            { id: '4', message: 'Prescription mise à jour', timestamp: 'Il y a 2 h' },
        ],
    };
}
