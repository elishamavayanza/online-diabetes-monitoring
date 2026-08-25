import { useEffect, useState } from 'react';
import { fetchActivities } from '../services/activityService';
import { ActivityItem } from '../types';

export function useActivity() {
    const [activities, setActivities] = useState<ActivityItem[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchActivities();
                setActivities(data);
            } catch (err) {
                setError('Impossible de charger l\'activité.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { activities, isLoading, error };
}
