import { NutritionistNotification, NotificationFilter } from '../types';

export async function fetchNotifications(filter: NotificationFilter): Promise<NutritionistNotification[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: NutritionistNotification[] = [
        { id: '1', titre: 'Nouveau rendez-vous', message: 'Rendez-vous avec Jean Dupont ajouté.', type: 'NEW_APPOINTMENT', estLue: false, date: '2026-08-25 09:00' },
        { id: '2', titre: 'Plan alimentaire modifié', message: 'Le plan de Marie Zawadi a été mis à jour.', type: 'PLAN_UPDATED', estLue: false, date: '2026-08-25 08:30' },
        { id: '3', titre: 'Nouveau message', message: 'Dr. Jean Dupont vous a envoyé un message.', type: 'NEW_MESSAGE', estLue: false, date: '2026-08-24 17:00' },
        { id: '4', titre: 'Rendez-vous bientôt', message: 'Rendez-vous dans 30 minutes.', type: 'APPOINTMENT_SOON', estLue: false, date: '2026-08-25 09:30' },
        { id: '5', titre: 'Patient ajouté', message: 'Alice M. a été ajoutée à votre équipe.', type: 'PATIENT_ADDED', estLue: true, date: '2026-08-23 14:00' },
    ];

    if (filter === 'Non lues') return all.filter(n => !n.estLue);
    return all;
}
