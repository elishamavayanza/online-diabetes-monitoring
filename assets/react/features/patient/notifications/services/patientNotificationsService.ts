import { PatientNotification, PatientNotificationFilter } from '../types';

export async function fetchPatientNotifications(filter: PatientNotificationFilter): Promise<PatientNotification[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: PatientNotification[] = [
        { id: '1', titre: 'Prise de médicament', message: 'Insuline à 18:00.', type: 'MEDICATION_REMINDER', estLue: false, date: '2026-08-25 09:00' },
        { id: '2', titre: 'Rendez-vous', message: 'Consultation demain à 10:00.', type: 'APPOINTMENT', estLue: false, date: '2026-08-25 08:00' },
        { id: '3', titre: 'Nouveau message', message: 'Dr. Dupont vous a écrit.', type: 'NEW_MESSAGE', estLue: false, date: '2026-08-24 17:00' },
        { id: '4', titre: 'Prescription mise à jour', message: 'Votre traitement a été modifié.', type: 'PRESCRIPTION_UPDATED', estLue: true, date: '2026-08-23 14:00' },
        { id: '5', titre: 'Rappel de mesure', message: 'Pensez à mesurer votre glycémie.', type: 'MEASUREMENT_REMINDER', estLue: false, date: '2026-08-25 07:30' },
    ];

    if (filter === 'Non lues') return all.filter(n => !n.estLue);
    return all;
}
