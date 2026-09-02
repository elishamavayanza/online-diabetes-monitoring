// agendaService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback'; // Ajout de ApiFeedback
import { AgendaData, AgendaDay, AgendaAppointment } from '../types';

interface BackendAppointment {
    id: string;
    scheduledAt: string;
    durationMinutes?: number;
    status: string;
    reason?: string;
    patient?: {
        fullName?: string;
    } | null;
}

export async function fetchAgenda(): Promise<AgendaData> {
    // 1. Récupérer les rendez-vous du professionnel connecté
    const response = await apiClient.get<ApiFeedback<BackendAppointment[]>>('/appointments/mine');
    const appointments = unwrapApiData<BackendAppointment[]>(
        response.data,
        "Erreur lors du chargement de l'agenda."
    );

    // 2. Filtrer éventuellement pour la semaine en cours (optionnel)
    const now = new Date();
    const startOfWeek = new Date(now);
    startOfWeek.setDate(now.getDate() - now.getDay() + 1); // lundi
    startOfWeek.setHours(0, 0, 0, 0);
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);

    const weekAppointments = appointments.filter((appt) => {
        const date = new Date(appt.scheduledAt);
        return date >= startOfWeek && date <= endOfWeek;
    });

    // 3. Grouper par jour et construire la structure AgendaDay
    const daysMap = new Map<string, AgendaDay>();

    // Initialiser les 7 jours de la semaine
    for (let i = 0; i < 7; i++) {
        const date = new Date(startOfWeek);
        date.setDate(startOfWeek.getDate() + i);
        const dateStr = date.toISOString().split('T')[0]; // format YYYY-MM-DD
        const dayLabel = date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' });
        daysMap.set(dateStr, {
            date: dateStr,
            label: dayLabel,
            appointments: [],
        });
    }

    // Remplir les rendez-vous
    weekAppointments.forEach((appt) => {
        const dateObj = new Date(appt.scheduledAt);
        const dateStr = dateObj.toISOString().split('T')[0];
        if (!daysMap.has(dateStr)) return; // sécurité

        const time = dateObj.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const patientName = appt.patient?.fullName ?? 'Patient inconnu';
        const motif = appt.reason ?? 'Consultation';
        const type = (appt.reason ?? '').toLowerCase().includes('diabète') ? 'Suivi diabète' : 'Consultation';

        const appointment: AgendaAppointment = {
            id: appt.id,
            time,
            patient: patientName,
            motif,
            type: type as AgendaAppointment['type'],
        };

        daysMap.get(dateStr)?.appointments.push(appointment);
    });

    // 4. Convertir la map en tableau
    const days = Array.from(daysMap.values()).sort((a, b) => a.date.localeCompare(b.date));

    return { days };
}
