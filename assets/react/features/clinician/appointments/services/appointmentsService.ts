// appointmentsService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback'; // ✅ Import de ApiFeedback
import { Appointment, AppointmentFilter } from '../types';

interface BackendAppointment {
    id: string;
    patientId: string;          // ✅ au lieu de `patient`
    scheduledAt: string;
    status: string;
    reason?: string;
    durationMinutes?: number;
}

export async function fetchAppointments(filter: AppointmentFilter): Promise<Appointment[]> {
    const response = await apiClient.get<ApiFeedback<BackendAppointment[]>>('/appointments/mine');
    const appointments = unwrapApiData<BackendAppointment[]>(
        response.data,
        "Erreur lors du chargement des rendez-vous."
    );

    // Récupérer les patientId uniques
    const patientIds = [...new Set(appointments.map(a => a.patientId))];
    // Récupérer les noms en parallèle
    const patientNames = await Promise.all(
        patientIds.map(id => fetchPatientName(id).catch(() => 'Patient inconnu'))
    );
    const nameMap = new Map(patientIds.map((id, index) => [id, patientNames[index]]));

    const normalized: Appointment[] = appointments.map((appt) => {
        const scheduled = new Date(appt.scheduledAt);
        const date = scheduled.toISOString().split('T')[0];
        const time = scheduled.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

        const statusMapping: Record<string, Appointment['statut']> = {
            SCHEDULED: 'En attente',
            CONFIRMED: 'Confirmé',
            COMPLETED: 'Terminé',
            CANCELLED: 'Annulé',
            NO_SHOW: 'Annulé',
        };

        return {
            id: appt.id,
            patientId: appt.patientId,                             // ✅ ajout
            patient: nameMap.get(appt.patientId) || 'Patient inconnu', // nom récupéré
            date,
            heure: time,
            motif: appt.reason || 'Consultation',
            statut: statusMapping[appt.status] ?? 'En attente',
        };
    });

    const todayStr = new Date().toISOString().split('T')[0];
    switch (filter) {
        case 'today':
            return normalized.filter((a) => a.date === todayStr);
        case 'upcoming':
            return normalized.filter(
                (a) => a.date > todayStr && (a.statut === 'Confirmé' || a.statut === 'En attente')
            );
        case 'completed':
            return normalized.filter((a) => a.statut === 'Terminé');
        case 'cancelled':
            return normalized.filter((a) => a.statut === 'Annulé');
        default:
            return normalized;
    }
}
export async function createAppointmentReminder(
    appointmentId: string,
    scheduledFor: string,
    channel: string = 'SMS'
) {
    //  Typer la réponse avec ApiFeedback<unknown>
    const response = await apiClient.post<ApiFeedback<unknown>>('/appointment-reminders', {
        appointmentId,
        channel,
        scheduledFor,
    });
    return unwrapApiData(response.data, 'Erreur lors de la programmation du rappel.');
}
export async function fetchPatientName(patientId: string): Promise<string> {
    try {
        const response = await apiClient.get<ApiFeedback<{ fullName?: string; firstName?: string; lastName?: string }>>(
            `/patients/${patientId}/profile`
        );
        const data = unwrapApiData(response.data, 'Erreur lors de la récupération du patient.');

        // Selon comment votre backend renvoie le nom, on s'adapte :
        if (data.fullName) return data.fullName;
        if (data.firstName || data.lastName) return `${data.firstName ?? ''} ${data.lastName ?? ''}`.trim();

        return 'Patient inconnu';
    } catch (error) {
        console.error(`Erreur pour le patient ${patientId}:`, error);
        return 'Patient inconnu';
    }
}

