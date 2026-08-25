import { useEffect, useState } from 'react';
import { fetchPatientNotifications } from '../services/patientNotificationsService';
import { PatientNotification, PatientNotificationFilter } from '../types';

export function usePatientNotifications() {
    const [notifications, setNotifications] = useState<PatientNotification[]>([]);
    const [filter, setFilter] = useState<PatientNotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchPatientNotifications(filter);
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
