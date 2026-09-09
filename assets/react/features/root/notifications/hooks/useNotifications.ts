import { useCallback, useEffect, useState } from 'react';
import { fetchNotifications, markNotificationAsRead } from '../services/notificationsService';
import { Notification, NotificationFilter } from '../types';

export function useNotifications() {
    const [notifications, setNotifications] = useState<Notification[]>([]);
    const [filter, setFilter] = useState<NotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const reload = useCallback(async (currentFilter: NotificationFilter = filter) => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchNotifications(currentFilter);
            setNotifications(data);
        } catch (err) {
            setError('Impossible de charger les notifications.');
        } finally {
            setIsLoading(false);
        }
    }, [filter]);

    useEffect(() => {
        reload();
    }, [reload, filter]);

    const markAsRead = useCallback(async (id: string) => {
        try {
            await markNotificationAsRead(id);
            setNotifications((prev) =>
                prev.map((n) => (n.id === id ? { ...n, estLue: true } : n))
            );
        } catch {
            setError('Erreur lors du marquage de la notification.');
        }
    }, []);

    return { notifications, filter, setFilter, markAsRead, reload, isLoading, error };
}