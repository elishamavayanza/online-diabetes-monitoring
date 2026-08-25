import { useEffect, useState } from 'react';
import { fetchNotifications } from '../services/notificationsService';
import { NutritionistNotification, NotificationFilter } from '../types';

export function useNotifications() {
    const [notifications, setNotifications] = useState<NutritionistNotification[]>([]);
    const [filter, setFilter] = useState<NotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchNotifications(filter);
                setNotifications(data);
            } catch (err) {
                setError('Impossible de charger les notifications.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [filter]);

    return { notifications, filter, setFilter, isLoading, error };
}
