import { ClinicianNotification, ClinicianNotificationFilter } from '../types';

export async function fetchClinicianNotifications(filter: ClinicianNotificationFilter): Promise<ClinicianNotification[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: ClinicianNotification[] = [
        {
            id: '1',
            titre: 'Prescription mise à jour',
            message: 'La prescription de Jean Dupont a été modifiée.',
            type: 'PRESCRIPTION_UPDATED',
            estLue: false,
            date: '2026-08-25 09:30',
        },
        {
            id: '2',
            titre: 'Nouveau rendez-vous',
            message: 'Rendez-vous ajouté avec Marie Zawadi.',
            type: 'NEW_APPOINTMENT',
            estLue: false,
            date: '2026-08-25 08:45',
        },
        {
            id: '3',
            titre: 'Rendez-vous dans 30 minutes',
            message: 'Patient A à 10:30.',
            type: 'APPOINTMENT_IN_30_MIN',
            estLue: false,
            date: '2026-08-25 10:00',
        },
        {
            id: '4',
            titre: 'Nouveau message',
            message: 'Nutritionniste Sarah K. vous a écrit.',
            type: 'NEW_MESSAGE',
            estLue: true,
            date: '2026-08-24 16:45',
        },
        {
            id: '5',
            titre: 'Patient ajouté à votre équipe',
            message: 'Paul K. a rejoint votre équipe.',
            type: 'PATIENT_ADDED_TO_TEAM',
            estLue: true,
            date: '2026-08-23 11:20',
        },
    ];

    if (filter === 'Non lues') {
        return all.filter((n) => !n.estLue);
    }
    return all;
}
