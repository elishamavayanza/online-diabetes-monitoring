import { getDateFormatLocale } from '@/react/i18n/dateLocale';
import apiClient from "@/services/api/client";
import { ClinicianDashboardData, FollowUpPatient, UpcomingAppointment } from '../types';
import { formatDateToApi } from '@/utils/date.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

const DAY_MS = 1000 * 60 * 60 * 24;

function isActive(status?: unknown): boolean {
    return String(status ?? '').toUpperCase() === 'ACTIVE';
}

function dateLabel(date: Date, now: Date): { label: string; isToday: boolean } {
    const startOfToday = new Date(now);
    startOfToday.setHours(0, 0, 0, 0);
    const startOfDate = new Date(date);
    startOfDate.setHours(0, 0, 0, 0);
    const diffDays = Math.round((startOfDate.getTime() - startOfToday.getTime()) / DAY_MS);

    if (diffDays <= 0) return { label: "Aujourd'hui", isToday: true };
    if (diffDays === 1) return { label: 'Demain', isToday: false };
    if (diffDays < 7) {
        return { label: date.toLocaleDateString(getDateFormatLocale(), { weekday: 'short' }), isToday: false };
    }
    return {
        label: date.toLocaleDateString(getDateFormatLocale(), { day: '2-digit', month: '2-digit' }),
        isToday: false,
    };
}

export async function fetchClinicianDashboardData(): Promise<ClinicianDashboardData> {
    try {
        const [patientsRes, appointmentsRes, externalFollowsRes] = await Promise.all([
            apiClient.get<ApiFeedback<any[]>>('/patients/assigned'),
            apiClient.get<ApiFeedback<any[]>>('/appointments/mine'),
            apiClient.get<ApiFeedback<any[]>>('/external-follows/my'),
        ]);

        const patients = patientsRes.data.data ?? [];
        const appointments = appointmentsRes.data.data ?? [];
        const externalFollows = externalFollowsRes.data.data ?? [];

        const patientMap = new Map(patients.map((p: any) => [String(p.id), p.fullName]));

        const now = new Date();
        const todayStr = formatDateToApi(now);

        const sortedAsc = [...appointments].sort(
            (a, b) => new Date(a.scheduledAt).getTime() - new Date(b.scheduledAt).getTime()
        );

        const appointmentsToday = appointments
            .filter((appt: any) => formatDateToApi(new Date(appt.scheduledAt)) === todayStr)
            .map((appt: any) => {
                const apptDate = new Date(appt.scheduledAt);
                return {
                    id: String(appt.id ?? ''),
                    time: apptDate.toLocaleTimeString(getDateFormatLocale(), { hour: '2-digit', minute: '2-digit' }),
                    patient: patientMap.get(String(appt.patientId)) ?? `Patient #${appt.patientId}`,
                };
            });

        const upcomingAppointments: UpcomingAppointment[] = sortedAsc
            .filter((appt: any) => new Date(appt.scheduledAt) > now)
            .slice(0, 6)
            .map((appt: any) => {
                const apptDate = new Date(appt.scheduledAt);
                const { label, isToday } = dateLabel(apptDate, now);
                return {
                    id: String(appt.id ?? ''),
                    date: label,
                    time: apptDate.toLocaleTimeString(getDateFormatLocale(), { hour: '2-digit', minute: '2-digit' }),
                    patient: patientMap.get(String(appt.patientId)) ?? `Patient #${appt.patientId}`,
                    reason: appt.reason ?? undefined,
                    isToday,
                };
            });

        const upcomingPatientIds = new Set(
            appointments
                .filter((appt: any) => new Date(appt.scheduledAt) > now)
                .map((appt: any) => String(appt.patientId))
        );

        const lastVisitByPatient = new Map<string, Date>();
        appointments
            .filter((appt: any) => new Date(appt.scheduledAt) <= now)
            .forEach((appt: any) => {
                const patientId = String(appt.patientId);
                const scheduledAt = new Date(appt.scheduledAt);
                const current = lastVisitByPatient.get(patientId);
                if (!current || scheduledAt.getTime() > current.getTime()) {
                    lastVisitByPatient.set(patientId, scheduledAt);
                }
            });

        const followUpPatients: FollowUpPatient[] = patients
            .filter((p: any) => isActive(p.status) && !upcomingPatientIds.has(String(p.id)))
            .map((p: any) => ({
                id: String(p.id),
                name: p.fullName ?? `Patient #${p.id}`,
                lastVisit:
                    lastVisitByPatient.get(String(p.id))?.toLocaleDateString(getDateFormatLocale(), {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                    }) ?? 'Jamais consulté',
            }));

        const recentActivities = appointments
            .filter((appt: any) => new Date(appt.scheduledAt) <= now)
            .slice(0, 5)
            .map((appt: any) => ({
                id: String(appt.id ?? ''),
                message: `Rendez-vous avec ${patientMap.get(String(appt.patientId)) ?? 'patient'}`,
                timestamp: new Date(appt.scheduledAt).toLocaleString('fr-FR'),
            }));

        const stats = [
            { id: 'patients', label: 'Mes patients', value: patients.length },
            { id: 'appointments-today', label: "Rendez-vous aujourd'hui", value: appointmentsToday.length },
            { id: 'appointments-upcoming', label: 'Rendez-vous à venir', value: upcomingPatientIds.size },
            { id: 'follow-up-needed', label: 'Patients nécessitant un suivi', value: followUpPatients.length },
        ];

        if (externalFollows.length > 0) {
            stats.push({ id: 'external-follows', label: 'Suivis externes', value: externalFollows.length });
        }

        return {
            stats,
            appointmentsToday,
            upcomingAppointments,
            followUpPatients,
            recentActivities,
        };
    } catch (error) {
        console.error('Erreur fetchClinicianDashboardData:', error);
        throw error;
    }
}