// services/adminDashboardService.ts
import apiClient from "@/services/api/client";
import { AdminDashboardData } from '../types';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

/**
 * Récupère l'ID de l'organisation depuis le token JWT.
 */
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

/**
 * Récupère les données du tableau de bord administrateur en agrégeant plusieurs endpoints.
 */
export async function fetchAdminDashboardData(): Promise<AdminDashboardData> {
    const organizationId = getOrganizationIdFromToken();
    if (!organizationId) {
        throw new Error('Organisation introuvable');
    }

    try {
        // Appels parallèles
        const [patientsRes, professionalsRes, usersRes, appointmentsRes] = await Promise.all([
            apiClient.get<ApiFeedback<any[]>>('/patients'),
            apiClient.get<ApiFeedback<any[]>>('/professionals'),
            apiClient.get<ApiFeedback<any[]>>('/users'),
            apiClient.get<ApiFeedback<any[]>>(`/appointments/queries/organization/${organizationId}`),
        ]);

        const patients = patientsRes.data.data ?? [];
        const professionals = professionalsRes.data.data ?? [];
        const users = usersRes.data.data ?? [];
        const appointments = appointmentsRes.data.data ?? [];

        // Stats
        const totalPatients = patients.length;
        const totalProfessionals = professionals.length;
        const totalMembers = users.length;
        const activePatients = patients.filter((p: any) => p.active === true).length;
        const activeProfessionals = professionals.filter((p: any) => p.active === true).length;

        // Rendez-vous aujourd'hui
        const today = new Date().toISOString().slice(0, 10);
        const appointmentsToday = appointments.filter((a: any) => {
            const apptDate = (a.scheduledAt ?? '').slice(0, 10);
            return apptDate === today;
        });

        // Établissements et départements : récupérés depuis les organisations ?
        // Pour l'instant, on initialise à 0 (à adapter si endpoint spécifique)
        const totalEstablishments = 0;
        const totalDepartments = 0;

        const stats = [
            { id: 'patients', label: 'Patients', value: totalPatients },
            { id: 'professionals', label: 'Professionnels', value: totalProfessionals },
            { id: 'members', label: 'Membres', value: totalMembers },
            { id: 'appointments-today', label: "Rendez-vous aujourd'hui", value: appointmentsToday.length },
            { id: 'establishments', label: 'Établissements', value: totalEstablishments },
            { id: 'departments', label: 'Départements', value: totalDepartments },
        ];

        // Activités récentes : à construire si un endpoint existe, sinon tableau vide
        const recentActivities: any[] = [];

        // Rendez-vous du jour formatés pour l'affichage
        const formattedAppointments = appointmentsToday.map((a: any) => ({
            id: String(a.id ?? ''),
            time: a.scheduledAt ? a.scheduledAt.slice(11, 16) : '',
            doctor: a.professionalName ?? a.doctorName ?? '—',
            patient: a.patientName ?? '—',
        }));

        const organizationStatus = [
            { id: 'active-professionals', label: 'Professionnels actifs', isActive: activeProfessionals > 0 },
            { id: 'active-patients', label: 'Patients actifs', isActive: activePatients > 0 },
            { id: 'active-establishments', label: 'Établissements actifs', isActive: true },
        ];

        return {
            stats,
            recentActivities,
            appointmentsToday: formattedAppointments,
            organizationStatus,
        };
    } catch (error) {
        console.error('Erreur fetchAdminDashboardData:', error);
        throw error;
    }
}
