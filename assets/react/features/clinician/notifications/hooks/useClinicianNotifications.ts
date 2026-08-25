import { useEffect, useState } from 'react';
import { fetchClinicianNotifications } from '../services/clinicianNotificationsService';
import { ClinicianNotification, ClinicianNotificationFilter } from '../types';

export function useClinicianNotifications() {
    const [notifications, setNotifications] = useState<ClinicianNotification[]>([]);
    const [filter, setFilter] = useState<ClinicianNotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchClinicianNotifications(filter);
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
