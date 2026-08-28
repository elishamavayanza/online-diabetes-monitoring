// services/appointmentService.ts
import { Appointment, AppointmentPeriod } from '../types';

// Fonction utilitaire pour convertir une Date en chaîne YYYY-MM-DD
const toDateKey = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

function isPastAppointment(appt: Appointment, now: Date): boolean {
    const [year, month, day] = appt.date.split('-').map(Number);
    const [hour, minute] = appt.heure.split(':').map(Number);
    const apptDate = new Date(year, month - 1, day, hour, minute);
    return apptDate < now;
}

export async function fetchAppointments(period: AppointmentPeriod): Promise<Appointment[]> {
    // Simuler un appel API (remplacez par votre logique réelle)
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

    const now = new Date();

    switch (period) {
        case 'all':
            return all;
        case 'history':
            return all.filter((appt) => isPastAppointment(appt, now));
        case 'today':
            return all.filter((appt) => {
                const todayKey = toDateKey(now);
                return appt.date === todayKey && !isPastAppointment(appt, now);
            });
        case 'week':
            return all.filter((appt) => {
                const apptDate = new Date(appt.date);
                const diffDays = Math.floor((apptDate.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
                return diffDays >= 0 && diffDays < 7 && !isPastAppointment(appt, now);
            });
        case 'month':
            return all.filter((appt) => {
                const apptDate = new Date(appt.date);
                return apptDate.getMonth() === now.getMonth() && apptDate.getFullYear() === now.getFullYear() && !isPastAppointment(appt, now);
            });
        default:
            return all;
    }
}
