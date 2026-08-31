import apiClient from "@/services/api/client";
import { ClinicianDashboardData } from '../types';
import { formatDateToApi } from '@/utils/date.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

export async function fetchClinicianDashboardData(): Promise<ClinicianDashboardData> {
    try {
        const [patientsRes, appointmentsRes] = await Promise.all([
            apiClient.get<ApiFeedback<any[]>>('/patients/assigned'),
            apiClient.get<ApiFeedback<any[]>>('/appointments/mine'),
        ]);

        const patients = patientsRes.data.data ?? [];
        const appointments = appointmentsRes.data.data ?? [];

        console.log('Patients bruts:', JSON.stringify(patients, null, 2));
        console.log('Rendez-vous bruts:', JSON.stringify(appointments, null, 2));

        // Map patientId -> nom complet pour affichage
        const patientMap = new Map(patients.map((p: any) => [String(p.id), p.fullName]));

        const now = new Date();
        const todayStr = formatDateToApi(now);

        const appointmentsToday = appointments
            .filter((appt: any) => {
                const d = new Date(appt.scheduledAt);
                const localDate = formatDateToApi(d);
                return localDate === todayStr;
            })
            .map((appt: any) => {
                // ✅ On recrée un objet Date ici pour le formatage
                const apptDate = new Date(appt.scheduledAt);
                return {
                    id: String(appt.id ?? ''),
                    time: apptDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                    patient: patientMap.get(String(appt.patientId)) ?? `Patient #${appt.patientId}`,
                };
            });

        const upcomingAppointments = appointments.filter((appt: any) => {
            return new Date(appt.scheduledAt) > now;
        });

        const stats = [
            { id: 'patients', label: 'Mes patients', value: patients.length },
            { id: 'appointments-today', label: "Rendez-vous aujourd'hui", value: appointmentsToday.length },
            { id: 'appointments-upcoming', label: 'Rendez-vous à venir', value: upcomingAppointments.length },
            { id: 'follow-up-needed', label: 'Patients nécessitant un suivi', value: 0 },
        ];

        console.log('Stats construites:', stats);
        console.log('Patients length:', patients.length);
        console.log('Appointments today:', appointmentsToday.length);
        console.log('Upcoming:', upcomingAppointments.length);

        const recentActivities = appointments.slice(0, 5).map((appt: any) => ({
            id: String(appt.id ?? ''),
            message: `Rendez-vous avec ${patientMap.get(String(appt.patientId)) ?? 'patient'}`,
            timestamp: new Date(appt.scheduledAt).toLocaleString('fr-FR'),
        }));

        return { stats, appointmentsToday, recentActivities };
    } catch (error) {
        console.error('Erreur fetchClinicianDashboardData:', error);
        throw error;
    }
}
