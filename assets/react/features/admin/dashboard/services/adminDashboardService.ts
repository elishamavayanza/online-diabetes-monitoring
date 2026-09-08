import apiClient from "@/services/api/client";
import { AdminDashboardData, AppointmentToday, UpcomingAppointment } from '../types';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

const DAY_MS = 1000 * 60 * 60 * 24;
const MAX_FACILITIES_FOR_DEPARTMENTS = 10;

function getOrganizationIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        const orgs = payload?.organizations;
        if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
            return String(orgs[0].organization_id);
        }
    } catch (e) {
        console.error('Erreur décodage token:', e);
    }
    return null;
}

function isActive(status?: unknown): boolean {
    return String(status ?? '').toUpperCase() === 'ACTIVE';
}

function dateLabel(date: Date, now: Date): string {
    const startOfToday = new Date(now);
    startOfToday.setHours(0, 0, 0, 0);
    const startOfDate = new Date(date);
    startOfDate.setHours(0, 0, 0, 0);
    const diffDays = Math.round((startOfDate.getTime() - startOfToday.getTime()) / DAY_MS);

    if (diffDays <= 0) return "Aujourd'hui";
    if (diffDays === 1) return 'Demain';
    if (diffDays < 7) return date.toLocaleDateString('fr-FR', { weekday: 'short' });
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
}

export async function fetchAdminDashboardData(): Promise<AdminDashboardData> {
    const organizationId = getOrganizationIdFromToken();
    if (!organizationId) {
        throw new Error('Organisation introuvable');
    }

    try {
        const [patientsRes, professionalsRes, usersRes, appointmentsRes, facilitiesRes] = await Promise.all([
            apiClient.get<ApiFeedback<any[]>>('/patients'),
            apiClient.get<ApiFeedback<any[]>>('/professionals'),
            apiClient.get<ApiFeedback<any[]>>('/users'),
            apiClient.get<ApiFeedback<any[]>>(`/appointments/queries/organization/${organizationId}`),
            apiClient.get<ApiFeedback<any[]>>(`/healthcare-facilities/organization/${organizationId}`),
        ]);

        const patients = patientsRes.data.data ?? [];
        const professionals = professionalsRes.data.data ?? [];
        const users = usersRes.data.data ?? [];
        const appointments = appointmentsRes.data.data ?? [];
        const facilities = facilitiesRes.data.data ?? [];

        // Total départements (agrégé par établissement)
        let totalDepartments = 0;
        try {
            const departmentsPerFacility = await Promise.allSettled(
                facilities.slice(0, MAX_FACILITIES_FOR_DEPARTMENTS).map((facility: any) =>
                    apiClient.get<ApiFeedback<any[]>>(`/departments/facility/${facility.id}`)
                )
            );
            totalDepartments = departmentsPerFacility.reduce((acc: number, result) => {
                return acc + (result.status === 'fulfilled' ? (result.value.data.data ?? []).length : 0);
            }, 0);
        } catch {
            totalDepartments = 0;
        }

        const patientNameById = new Map(patients.map((p: any) => [String(p.id), p.fullName]));
        const professionalNameById = new Map(professionals.map((pro: any) => [String(pro.id), pro.fullName]));

        const totalPatients = patients.length;
        const totalProfessionals = professionals.length;
        const totalMembers = users.length;
        const activePatients = patients.filter((p: any) => isActive(p.status)).length;
        const activeProfessionals = professionals.filter((pro: any) => isActive(pro.status)).length;

        const now = new Date();

        const toAppointment = (appt: any): { id: string; time: string; doctor: string; patient: string } => {
            const date = new Date(appt.scheduledAt);
            return {
                id: String(appt.id ?? ''),
                time: date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                doctor:
                    professionalNameById.get(String(appt.professionalId)) ??
                    appt.createdByName ??
                    '—',
                patient: patientNameById.get(String(appt.patientId)) ?? '—',
            };
        };

        const appointmentsToday: AppointmentToday[] = appointments
            .filter((appt: any) => {
                const d = new Date(appt.scheduledAt);
                const startOfDay = new Date(now);
                startOfDay.setHours(0, 0, 0, 0);
                return d >= startOfDay && d < new Date(startOfDay.getTime() + DAY_MS);
            })
            .map(toAppointment);

        const sortedAsc = [...appointments].sort(
            (a, b) => new Date(a.scheduledAt).getTime() - new Date(b.scheduledAt).getTime()
        );

        const upcomingAppointments: UpcomingAppointment[] = sortedAsc
            .filter((appt: any) => new Date(appt.scheduledAt) > now)
            .slice(0, 6)
            .map((appt: any) => ({ ...toAppointment(appt), date: dateLabel(new Date(appt.scheduledAt), now) }));

        const sevenDaysAgo = new Date(now.getTime() - 7 * DAY_MS);
        const recentActivities = appointments
            .filter((appt: any) => {
                const d = new Date(appt.scheduledAt);
                return d <= now && d >= sevenDaysAgo;
            })
            .sort((a, b) => new Date(b.scheduledAt).getTime() - new Date(a.scheduledAt).getTime())
            .slice(0, 6)
            .map((appt: any) => ({
                id: String(appt.id ?? ''),
                message: `Rendez-vous de ${patientNameById.get(String(appt.patientId)) ?? 'patient'} avec ${
                    professionalNameById.get(String(appt.professionalId)) ?? appt.createdByName ?? 'un professionnel'
                }`,
                timestamp: new Date(appt.scheduledAt).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                }),
            }));

        const stats = [
            { id: 'patients', label: 'Patients', value: totalPatients },
            { id: 'professionals', label: 'Professionnels', value: totalProfessionals },
            { id: 'members', label: 'Membres', value: totalMembers },
            { id: 'establishments', label: 'Établissements', value: facilities.length },
            { id: 'departments', label: 'Départements', value: totalDepartments },
            { id: 'appointments-today', label: "Rendez-vous aujourd'hui", value: appointmentsToday.length },
        ];

        const organizationStatus = [
            { id: 'active-professionals', label: 'Professionnels actifs', isActive: activeProfessionals > 0 },
            { id: 'active-patients', label: 'Patients actifs', isActive: activePatients > 0 },
            { id: 'active-establishments', label: 'Établissements actifs', isActive: facilities.length > 0 },
        ];

        return {
            stats,
            recentActivities,
            appointmentsToday,
            upcomingAppointments,
            organizationStatus,
        };
    } catch (error) {
        console.error('Erreur fetchAdminDashboardData:', error);
        throw error;
    }
}