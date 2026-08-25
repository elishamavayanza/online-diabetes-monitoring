import { DashboardData } from '../types';

export async function fetchDashboardData(): Promise<DashboardData> {
    // Simulation d'un délai réseau
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        stats: [
            { id: 'orgs', label: 'Organisations', value: 24 },
            { id: 'users', label: 'Utilisateurs', value: 1284 },
            { id: 'professionals', label: 'Professionnels', value: 386 },
            { id: 'patients', label: 'Patients', value: 842 },
            { id: 'active-orgs', label: 'Organisations actives', value: 21 },
        ],
        recentActivities: [
            { id: '1', message: 'Nouvelle organisation créée', timestamp: 'Il y a 5 min' },
            { id: '2', message: 'Nouveau professionnel enregistré', timestamp: 'Il y a 30 min' },
            { id: '3', message: 'Organisation désactivée', timestamp: 'Il y a 1 h' },
            { id: '4', message: 'Nouveau compte utilisateur', timestamp: 'Il y a 2 h' },
            { id: '5', message: 'Modification des permissions', timestamp: 'Il y a 3 h' },
        ],
        platformStatus: [
            { id: 'users-active', label: 'Utilisateurs actifs', isActive: true },
            { id: 'orgs-active', label: 'Organisations actives', isActive: true },
            { id: 'notifications', label: 'Notifications', isActive: true },
            { id: 'services', label: 'Services', isActive: true },
        ],
    };
}
