import { useEffect, useState } from 'react';
import { fetchAdminNotifications } from '../services/adminNotificationsService';
import { AdminNotification, AdminNotificationFilter } from '../types';

export function useAdminNotifications() {
    const [notifications, setNotifications] = useState<AdminNotification[]>([]);
    const [filter, setFilter] = useState<AdminNotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchAdminNotifications(filter);
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
