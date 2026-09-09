import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import {
    Notification,
    NotificationFilter,
    CreateSystemNotificationPayload,
} from '../types';

interface BackendNotification {
    id: string;
    type: string | null;
    title: string;
    body: string;
    channel: string | null;
    readAt: string | null;
    createdAt: string;
}

const typeMapping: Record<string, Notification['type']> = {
    MEDICATION_REMINDER: 'MEDICATION_REMINDER',
    APPOINTMENT_REMINDER: 'APPOINTMENT_REMINDER',
    MEASUREMENT_REMINDER: 'MEASUREMENT_REMINDER',
    MESSAGE_RECEIVED: 'MESSAGE_RECEIVED',
    PRESCRIPTION_UPDATED: 'PRESCRIPTION_UPDATED',
    SYSTEM_ALERT: 'SYSTEM_ALERT',
};

function mapNotification(n: BackendNotification): Notification {
    return {
        id: n.id,
        titre: n.title,
        message: n.body,
        type: typeMapping[n.type ?? ''] ?? 'SYSTEM_ALERT',
        estLue: !!n.readAt,
        date: n.createdAt ? new Date(n.createdAt).toLocaleString('fr-FR') : '',
    };
}

export async function fetchNotifications(filter: NotificationFilter): Promise<Notification[]> {
    const response = await apiClient.get<ApiFeedback<BackendNotification[]>>('/notifications/me');
    const data = unwrapApiData(response.data, 'Erreur lors du chargement des notifications.');

    const mapped = data.map(mapNotification);

    switch (filter) {
        case 'Non lues':
            return mapped.filter((n) => !n.estLue);
        case 'Alertes système':
            return mapped.filter((n) => n.type === 'SYSTEM_ALERT');
        default:
            return mapped;
    }
}

export async function publishSystemNotification(payload: CreateSystemNotificationPayload): Promise<void> {
    await apiClient.post<ApiFeedback<null>>('/notifications', payload);
}

export async function markNotificationAsRead(notificationId: string): Promise<void> {
    await apiClient.patch<ApiFeedback<null>>(`/notifications/${notificationId}/read`);
}