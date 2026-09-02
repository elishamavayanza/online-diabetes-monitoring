// services/clinicianNotificationsService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { ClinicianNotification, ClinicianNotificationFilter, ClinicianNotificationType } from '../types';

interface BackendNotification {
    id: string;
    type: string;
    title: string;
    body: string;
    readAt: string | null;
    createdAt: string;
}

const typeMapping: Record<string, ClinicianNotificationType> = {
    PRESCRIPTION_UPDATED: 'PRESCRIPTION_UPDATED',
    NEW_APPOINTMENT: 'NEW_APPOINTMENT',
    APPOINTMENT_IN_30_MIN: 'APPOINTMENT_IN_30_MIN',
    NEW_MESSAGE: 'NEW_MESSAGE',
    PATIENT_ADDED_TO_TEAM: 'PATIENT_ADDED_TO_TEAM',
};

function mapType(type: string): ClinicianNotificationType {
    return typeMapping[type] ?? 'NEW_MESSAGE';
}

export async function fetchClinicianNotifications(
    filter: ClinicianNotificationFilter
): Promise<ClinicianNotification[]> {
    const response = await apiClient.get<ApiFeedback<BackendNotification[]>>('/notifications/me');
    const notifications = unwrapApiData(response.data, 'Erreur lors du chargement des notifications.');

    const mapped: ClinicianNotification[] = notifications.map((n) => ({
        id: n.id,
        titre: n.title,
        message: n.body,
        type: mapType(n.type),
        estLue: !!n.readAt,
        date: n.createdAt ? new Date(n.createdAt).toLocaleString('fr-FR') : '',
    }));

    if (filter === 'Non lues') {
        return mapped.filter((n) => !n.estLue);
    }
    return mapped;
}

export async function markNotificationAsRead(notificationId: string): Promise<void> {
    const response = await apiClient.patch<ApiFeedback<unknown>>(`/notifications/${notificationId}`, {
        readAt: new Date().toISOString(),
    });
    unwrapApiData(response.data, "Erreur lors du marquage de la notification comme lue.");
}
