import apiClient from '@/services/api/client';
import { NutritionistDashboardData } from '../types';
import { formatDateToApi } from '@/utils/date.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

export async function fetchNutritionistDashboardData(): Promise<NutritionistDashboardData> {
    const [patientsRes, appointmentsRes] = await Promise.all([
        apiClient.get<ApiFeedback<any[]>>('/patients/assigned'),
        apiClient.get<ApiFeedback<any[]>>('/appointments/mine'),
    ]);

    const patients = patientsRes.data.data ?? [];
    const appointments = appointmentsRes.data.data ?? [];
    const patientMap = new Map(patients.map((p: any) => [String(p.id), p.fullName]));

    const now = new Date();
    const todayStr = formatDateToApi(now);

    const appointmentsToday = appointments
        .filter((appt: any) => {
            const d = new Date(appt.scheduledAt);
            return formatDateToApi(d) === todayStr;
        })
        .map((appt: any) => {
            const apptDate = new Date(appt.scheduledAt);
            return {
                id: String(appt.id ?? ''),
                time: apptDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                patient: patientMap.get(String(appt.patientId)) ?? `Patient #${appt.patientId}`,
            };
        });

    const upcomingAppointments = appointments.filter((appt: any) => new Date(appt.scheduledAt) > now);

    const stats = [
        { id: 'patients', label: 'Mes patients', value: patients.length },
        { id: 'appointments-today', label: "Rendez-vous aujourd'hui", value: appointmentsToday.length },
        { id: 'appointments-upcoming', label: 'Rendez-vous à venir', value: upcomingAppointments.length },
        { id: 'follow-up-needed', label: 'Patients nécessitant un suivi', value: 0 },
    ];

    const recentActivities = appointments.slice(0, 5).map((appt: any) => ({
        id: String(appt.id ?? ''),
        message: `Rendez-vous avec ${patientMap.get(String(appt.patientId)) ?? 'patient'}`,
        timestamp: new Date(appt.scheduledAt).toLocaleString('fr-FR'),
    }));

    return { stats, appointmentsToday, recentActivities };
}
