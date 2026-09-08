import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import {
    AgendaAppointment,
    AgendaData,
    AgendaDay,
    AgendaRecord,
    AgendaStat,
} from '../types';
import { formatDateToApi } from '@/utils/date.utils';

export async function fetchAgenda(): Promise<AgendaData> {
    // Les rendez-vous arrivant sur /appointments/mine ne contiennent que patientId.
    // On récupère les patients assignés pour afficher leurs vrais noms.
    const [appointmentsRes, patientsRes] = await Promise.all([
        apiClient.get<ApiFeedback<any[]>>('/appointments/mine'),
        apiClient.get<ApiFeedback<any[]>>('/patients/assigned').catch(() => null),
    ]);

    const appointments = unwrapApiData<any[]>(
        appointmentsRes.data,
        "Erreur lors du chargement de l'agenda."
    );
    const patients = patientsRes?.data?.data ?? [];

    const patientMap = new Map(patients.map((p: any) => [String(p.id), p.fullName]));
    const patientName = (patientId: any): string =>
        patientMap.get(String(patientId)) ?? `Patient #${patientId}`;

    const now = new Date();

    const startOfWeek = new Date(now);
    startOfWeek.setDate(now.getDate() - now.getDay() + 1); // lundi
    startOfWeek.setHours(0, 0, 0, 0);
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);

    const weekAppointments = appointments
        .filter((appt: any) => {
            const date = new Date(appt.scheduledAt);
            return date >= startOfWeek && date <= endOfWeek;
        })
        .map((appt: any) => {
            const date = new Date(appt.scheduledAt);
            return {
                dateStr: formatDateToApi(date),
                appointment: toAgendaAppointment(appt, date, patientName, now),
            };
        });

    const daysMap = new Map<string, AgendaDay>();
    for (let i = 0; i < 7; i++) {
        const date = new Date(startOfWeek);
        date.setDate(startOfWeek.getDate() + i);
        const dateStr = formatDateToApi(date);
        const dayLabel = date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' });
        daysMap.set(dateStr, {
            date: dateStr,
            label: dayLabel,
            appointments: [],
        });
    }

    weekAppointments.forEach(({ dateStr, appointment }) => {
        if (!daysMap.has(dateStr)) return;
        daysMap.get(dateStr)?.appointments.push(appointment);
    });

    daysMap.forEach((day) => {
        day.appointments.sort((a, b) => a.time.localeCompare(b.time));
    });

    const records: AgendaRecord[] = appointments
        .map((appt: any) => {
            const date = new Date(appt.scheduledAt);
            return {
                id: String(appt.id ?? ''),
                date: formatDateToApi(date),
                dateTime: appt.scheduledAt,
                time: date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                patient: patientName(appt.patientId),
                motif: appt.reason ?? 'Consultation',
                status: appt.status ?? undefined,
                isPast: date.getTime() < now.getTime(),
            };
        })
        .sort((a, b) => a.dateTime.localeCompare(b.dateTime));

    const todayStr = formatDateToApi(now);
    const todayAppointments = weekAppointments.filter((appt) => appt.dateStr === todayStr);

    const stats: AgendaStat[] = [
        { id: 'today', label: "Aujourd'hui", value: todayAppointments.length },
        { id: 'week', label: 'Cette semaine', value: weekAppointments.length },
        { id: 'upcoming', label: 'À venir', value: records.filter((r) => !r.isPast).length },
    ];

    return {
        days: Array.from(daysMap.values()).sort((a, b) => a.date.localeCompare(b.date)),
        records,
        stats,
    };
}

function toAgendaAppointment(
    appt: any,
    date: Date,
    patientName: (patientId: any) => string,
    now: Date
): AgendaAppointment {
    const type = (appt.reason ?? '').toLowerCase().includes('diabète')
        ? ('Suivi diabète' as const)
        : ('Consultation' as const);

    return {
        id: String(appt.id ?? ''),
        time: date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
        patient: patientName(appt.patientId),
        patientId: String(appt.patientId ?? ''),
        motif: appt.reason ?? 'Consultation',
        type,
        status: appt.status ?? undefined,
        durationMinutes: appt.durationMinutes,
        isPast: date.getTime() < now.getTime(),
    };
}