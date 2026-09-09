import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { AdminNotification, AdminNotificationFilter, AdminNotificationType } from '../types';

interface BackendNotification {
    id: string;
    type: string | null;
    title: string;
    body: string;
    channel: string | null;
    readAt: string | null;
    createdAt: string;
}

const typeMapping: Record<string, AdminNotificationType> = {
    SYSTEM_ALERT: 'SYSTEM_ALERT',
    MESSAGE_RECEIVED: 'MESSAGE_RECEIVED',
    APPOINTMENT_REMINDER: 'APPOINTMENT_CREATED',
};

function mapNotification(n: BackendNotification): AdminNotification {
    return {
        id: n.id,
        titre: n.title,
        message: n.body,
        type: typeMapping[n.type ?? ''] ?? 'SYSTEM_ALERT',
        estLue: !!n.readAt,
        date: n.createdAt ? new Date(n.createdAt).toLocaleString('fr-FR') : '',
    };
}

export async function fetchAdminNotifications(filter: AdminNotificationFilter): Promise<AdminNotification[]> {
    const response = await apiClient.get<ApiFeedback<BackendNotification[]>>('/notifications/me');
    const data = unwrapApiData(response.data, 'Erreur lors du chargement des notifications.');

    const mapped = data.map(mapNotification);

    switch (filter) {
        case 'Non lues':
            return mapped.filter((n) => !n.estLue);
        case 'Système':
            return mapped.filter((n) => n.type === 'SYSTEM_ALERT');
        default:
            return mapped;
    }
}

export async function markAdminNotificationAsRead(notificationId: string): Promise<void> {
    await apiClient.patch<ApiFeedback<null>>(`/notifications/${notificationId}/read`);
}