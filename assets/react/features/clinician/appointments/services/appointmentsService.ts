import { Appointment, AppointmentFilter } from '../types';

export async function fetchAppointments(filter: AppointmentFilter): Promise<Appointment[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: Appointment[] = [
        {
            id: '1',
            patient: 'Jean Dupont',
            date: '2026-08-25',
            heure: '08:00',
            motif: 'Consultation',
            statut: 'Confirmé',
        },
        {
            id: '2',
            patient: 'Marie X',
            date: '2026-08-25',
            heure: '10:30',
            motif: 'Suivi diabète',
            statut: 'En attente',
        },
        {
            id: '3',
            patient: 'Paul K.',
            date: '2026-08-26',
            heure: '09:00',
            motif: 'Consultation',
            statut: 'Confirmé',
        },
        {
            id: '4',
            patient: 'Alice M.',
            date: '2026-08-20',
            heure: '11:00',
            motif: 'Consultation',
            statut: 'Terminé',
        },
        {
            id: '5',
            patient: 'Patient C',
            date: '2026-08-19',
            heure: '14:00',
            motif: 'Suivi',
            statut: 'Annulé',
        },
    ];

    switch (filter) {
        case 'today':
            return all.filter((a) => a.date === '2026-08-25');
        case 'upcoming':
            return all.filter((a) => a.date > '2026-08-25' && a.statut === 'Confirmé');
        case 'completed':
            return all.filter((a) => a.statut === 'Terminé');
        case 'cancelled':
            return all.filter((a) => a.statut === 'Annulé');
        default:
            return all;
    }
}
