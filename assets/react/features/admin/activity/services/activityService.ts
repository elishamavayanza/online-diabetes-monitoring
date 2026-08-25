import { ActivityItem } from '../types';

export async function fetchActivities(): Promise<ActivityItem[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        { id: '1', message: 'Nouveau patient enregistré', timestamp: 'Il y a 10 min', type: 'success' },
        { id: '2', message: 'Rendez-vous confirmé', timestamp: 'Il y a 30 min', type: 'info' },
        { id: '3', message: 'Modification des permissions', timestamp: 'Il y a 1 h', type: 'warning' },
        { id: '4', message: 'Nouvel établissement créé', timestamp: 'Il y a 2 h', type: 'success' },
    ];
}
