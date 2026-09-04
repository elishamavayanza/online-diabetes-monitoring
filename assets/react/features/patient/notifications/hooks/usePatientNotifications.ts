// hooks/usePatientNotifications.ts
import { useEffect, useState, useCallback } from 'react';
import {
    fetchPatientNotifications,
    markNotificationAsRead,
} from '../services/patientNotificationsService';
import { PatientNotification, PatientNotificationFilter } from '../types';

export function usePatientNotifications() {
    const [notifications, setNotifications] = useState<PatientNotification[]>([]);
    const [filter, setFilter] = useState<PatientNotificationFilter>('Toutes');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadNotifications = useCallback(async (currentFilter = filter) => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchPatientNotifications(currentFilter);
            setNotifications(data);
        } catch (err) {
            setError('Impossible de charger les notifications.');
        } finally {
            setIsLoading(false);
        }
    }, [filter]);

    useEffect(() => {
        loadNotifications();
    }, [loadNotifications]);

    const markAsRead = useCallback(async (id: string) => {
        try {
            await markNotificationAsRead(id);
            // Optimistic update : marquer comme lue localement
            setNotifications((prev) =>
                prev.map((n) => (n.id === id ? { ...n, estLue: true } : n))
            );
            // Si le filtre est "Non lues", on peut recharger pour retirer la notification
            if (filter === 'Non lues') {
                loadNotifications();
            }
        } catch (err) {
            setError("Erreur lors du marquage de la notification.");
        }
    }, [filter, loadNotifications]);

    return {
        notifications,
        filter,
        setFilter,
        markAsRead,
        isLoading,
        error,
    };
}
