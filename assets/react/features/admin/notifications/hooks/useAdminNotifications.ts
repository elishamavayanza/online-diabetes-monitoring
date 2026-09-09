import { useCallback, useEffect, useState } from 'react';
import { fetchAdminNotifications, markAdminNotificationAsRead } from '../services/adminNotificationsService';
import { AdminNotification, AdminNotificationFilter } from '../types';

export function useAdminNotifications() {
    const [notifications, setNotifications] = useState<AdminNotification[]>([]);
    const [filter, setFilter] = useState<AdminNotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const reload = useCallback(async (currentFilter: AdminNotificationFilter = filter) => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchAdminNotifications(currentFilter);
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
            await markAdminNotificationAsRead(id);
            setNotifications((prev) =>
                prev.map((n) => (n.id === id ? { ...n, estLue: true } : n))
            );
        } catch {
            setError('Erreur lors du marquage de la notification.');
        }
    }, []);

    return { notifications, filter, setFilter, markAsRead, reload, isLoading, error };
}