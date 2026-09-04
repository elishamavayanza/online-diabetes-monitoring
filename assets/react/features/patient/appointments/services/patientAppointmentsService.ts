// services/patientAppointmentsService.ts
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken, getCurrentUserOrganizationId } from '@/react/utils/authUtils';
import { PatientAppointment, ProfessionalOption } from '../types';

interface BackendAppointment {
    id: string;
    scheduledAt: string;
    status: string;
    reason?: string;
    professionalName?: string;
    professionalId?: string;
}

interface BackendProfessional {
    id: string;
    fullName: string;
    organizationId?: string | null;
}

export async function fetchPatientAppointments(): Promise<PatientAppointment[]> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    // Récupérer l'équipe pour construire la correspondance id -> nom
    const team = await fetchPatientTeam(patientId);
    const professionalMap = new Map(team.map((p) => [p.id, p.fullName]));

    const response = await apiClient.get<ApiFeedback<BackendAppointment[]>>(
        `/appointments/queries/patient/${patientId}`
    );
    const appointments = unwrapApiData(response.data, 'Erreur lors du chargement des rendez-vous.');

    return appointments.map((appt) => {
        const scheduled = new Date(appt.scheduledAt);
        const dateStr = scheduled.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        });
        const timeStr = scheduled.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit',
        });

        const statusMapping: Record<string, PatientAppointment['statut']> = {
            SCHEDULED: 'En attente',
            CONFIRMED: 'Confirmé',
            COMPLETED: 'Terminé',
            CANCELLED: 'Annulé',
            NO_SHOW: 'Absent',
        };

        return {
            id: appt.id,
            date: dateStr,
            heure: timeStr,
            professionnel: professionalMap.get(appt.professionalId ?? '') ?? 'Professionnel',
            motif: appt.reason ?? 'Consultation',
            statut: statusMapping[appt.status] ?? 'En attente',
            scheduledAt: appt.scheduledAt, //  ISO string
        };
    });
}

//  Nouvelle fonction : récupérer les professionnels de l'organisation du patient
export async function fetchProfessionalsByOrganization(): Promise<ProfessionalOption[]> {
    const organizationId = getCurrentUserOrganizationId();
    const response = await apiClient.get<ApiFeedback<BackendProfessional[]>>('/professionals');
    const professionals = unwrapApiData(response.data, 'Erreur lors du chargement des professionnels.');

    // Filtrer par organisation si un organisationId est disponible
    return professionals
        .filter((p) => !organizationId || p.organizationId === organizationId)
        .map((p) => ({ id: p.id, fullName: p.fullName }));
}

export async function fetchPatientTeam(patientId: string): Promise<ProfessionalOption[]> {
    const response = await apiClient.get<ApiFeedback<any[]>>(`/patients/${patientId}/team`);
    const team = unwrapApiData(response.data, 'Erreur lors du chargement de votre équipe.');
    return team.map((member) => ({
        id: String(member.id),
        fullName: member.fullName ?? member.name ?? 'Professionnel',
    }));
}

export async function createAppointmentRequest(data: {
    patientId: string;
    professionalId: string;
    organizationId: string;
    scheduledAt: string;
    durationMinutes: number;
    reason?: string;
    notes?: string;
}): Promise<void> {
    const response = await apiClient.post<ApiFeedback<unknown>>('/appointments', {
        ...data,
        status: 'SCHEDULED',
    });
    unwrapApiData(response.data, "Erreur lors de la création de la demande de rendez-vous.");
}
