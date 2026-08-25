import { AdminNotification, AdminNotificationFilter, AdminNotificationType } from '../types';

export async function fetchAdminNotifications(filter: AdminNotificationFilter): Promise<AdminNotification[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: AdminNotification[] = [
        {
            id: '1',
            titre: 'Nouveau patient enregistré',
            message: 'Un patient a été ajouté à votre organisation.',
            type: 'APPOINTMENT_CREATED',
            estLue: false,
            date: '2026-08-25 09:10',
        },
        {
            id: '2',
            titre: 'Message de la plateforme',
            message: 'Mise à jour de sécurité planifiée.',
            type: 'SYSTEM_ALERT',
            estLue: false,
            date: '2026-08-24 18:00',
        },
        {
            id: '3',
            titre: 'Nouveau professionnel',
            message: 'Dr. Alice Martin a rejoint votre organisation.',
            type: 'APPOINTMENT_CREATED',
            estLue: true,
            date: '2026-08-23 14:30',
        },
    ];

    switch (filter) {
        case 'Non lues':
            return all.filter((n) => !n.estLue);
        case 'Système':
            return all.filter((n) => n.type === 'SYSTEM_ALERT');
        default:
            return all;
    }
}
