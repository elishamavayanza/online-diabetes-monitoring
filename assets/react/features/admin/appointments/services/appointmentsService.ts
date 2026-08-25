import { Appointment, AppointmentPeriod } from '../types';

export async function fetchAppointments(period: AppointmentPeriod): Promise<Appointment[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: Appointment[] = [
        {
            id: '1',
            patient: 'Marie Zawadi',
            professionnel: 'Dr. Jean Dupont',
            etablissement: 'Hôpital Central',
            date: '2026-08-25',
            heure: '08:00',
            statut: 'Confirmed',
        },
        {
            id: '2',
            patient: 'Jean-Pierre L.',
            professionnel: 'Nutritionniste Sarah K.',
            etablissement: 'Hôpital Central',
            date: '2026-08-25',
            heure: '09:30',
            statut: 'Pending',
        },
        {
            id: '3',
            patient: 'Alice M.',
            professionnel: 'Dr. Alice Martin',
            etablissement: 'Clinique du Lac',
            date: '2026-08-26',
            heure: '10:15',
            statut: 'Cancelled',
        },
    ];

    // Filtre simple selon la période (simulation)
    if (period === 'today') return all.filter((a) => a.date === '2026-08-25');
    if (period === 'week') return all; // à adapter
    return all;
}
