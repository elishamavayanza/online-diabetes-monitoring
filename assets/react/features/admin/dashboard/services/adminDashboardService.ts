import { AdminDashboardData } from '../types';

export async function fetchAdminDashboardData(): Promise<AdminDashboardData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        stats: [
            { id: 'patients', label: 'Patients', value: 842 },
            { id: 'professionals', label: 'Professionnels', value: 36 },
            { id: 'members', label: 'Membres', value: 52 },
            { id: 'appointments-today', label: 'Rendez-vous aujourd\'hui', value: 24 },
            { id: 'establishments', label: 'Établissements', value: 4 },
            { id: 'departments', label: 'Départements', value: 12 },
        ],
        recentActivities: [
            { id: '1', message: 'Nouveau professionnel ajouté', timestamp: 'Il y a 10 min' },
            { id: '2', message: 'Nouveau patient enregistré', timestamp: 'Il y a 30 min' },
            { id: '3', message: 'Nouveau membre ajouté', timestamp: 'Il y a 1 h' },
            { id: '4', message: 'Rendez-vous créé', timestamp: 'Il y a 2 h' },
            { id: '5', message: 'Patient affecté à une équipe', timestamp: 'Il y a 3 h' },
        ],
        appointmentsToday: [
            { id: '1', time: '08:00', doctor: 'Dr. X', patient: 'Patient Y' },
            { id: '2', time: '09:30', doctor: 'Dr. Z', patient: 'Patient A' },
            { id: '3', time: '10:15', doctor: 'Dr. W', patient: 'Patient B' },
        ],
        organizationStatus: [
            { id: 'active-professionals', label: 'Professionnels actifs', isActive: true },
            { id: 'active-patients', label: 'Patients actifs', isActive: true },
            { id: 'active-establishments', label: 'Établissements actifs', isActive: true },
        ],
    };
}
