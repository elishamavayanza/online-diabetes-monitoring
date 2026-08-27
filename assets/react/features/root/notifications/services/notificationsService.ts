import {
    Notification,
    NotificationFilter,
    CreateSystemNotificationPayload,
} from '../types';

export async function fetchNotifications(filter: NotificationFilter): Promise<Notification[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: Notification[] = [
        {
            id: '1',
            titre: 'Alerte système',
            message: 'Une mise à jour critique est disponible.',
            type: 'SYSTEM_ALERT',
            estLue: false,
            date: '2026-08-25 09:00',
        },
        {
            id: '2',
            titre: 'Message reçu',
            message: 'Nouveau message de la part du support.',
            type: 'MESSAGE_RECEIVED',
            estLue: false,
            date: '2026-08-24 17:45',
        },
        {
            id: '3',
            titre: 'Prescription mise à jour',
            message: 'Une prescription a été modifiée par Dr. Jean.',
            type: 'PRESCRIPTION_UPDATED',
            estLue: true,
            date: '2026-08-23 11:20',
        },
        {
            id: '4',
            titre: 'Alerte système',
            message: 'Sauvegarde automatique effectuée avec succès.',
            type: 'SYSTEM_ALERT',
            estLue: true,
            date: '2026-08-22 03:00',
        },
    ];

    switch (filter) {
        case 'Non lues':
            return all.filter((n) => !n.estLue);
        case 'Alertes système':
            return all.filter((n) => n.type === 'SYSTEM_ALERT');
        default:
            return all;
    }
}

export async function publishSystemNotification(payload: CreateSystemNotificationPayload): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Notification système publiée', payload);
    // Appel API réel à implémenter
}
