// services/patientNotificationsService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { PatientNotification, PatientNotificationFilter } from '../types';

// Mapping des types backend vers les types frontend
const mapBackendType = (backendType: string): PatientNotification['type'] => {
    switch (backendType) {
        case 'MEDICATION_REMINDER':
            return 'MEDICATION_REMINDER';
        case 'APPOINTMENT':
            return 'APPOINTMENT';
        case 'NEW_MESSAGE':
            return 'NEW_MESSAGE';
        case 'PRESCRIPTION_UPDATED':
            return 'PRESCRIPTION_UPDATED';
        case 'MEASUREMENT_REMINDER':
            return 'MEASUREMENT_REMINDER';
        default:
            // Si le type n'est pas reconnu, on le met dans NEW_MESSAGE ou on le laisse ?
            // On peut aussi retourner 'NEW_MESSAGE' par défaut.
            return 'NEW_MESSAGE';
    }
};

// Formater la date si nécessaire (le backend renvoie probablement un ISO string)
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toISOString().slice(0, 19).replace('T', ' '); // format 'YYYY-MM-DD HH:mm'
};

export async function fetchPatientNotifications(
    filter: PatientNotificationFilter
): Promise<PatientNotification[]> {
    // Appel à l'API pour récupérer les notifications de l'utilisateur connecté
    const response = await apiClient.get<ApiFeedback<any[]>>('/notifications/me');
    const notifications = unwrapApiData(response.data, 'Erreur lors du chargement des notifications.');

    // Mapping des données backend vers le type PatientNotification
    const mapped = notifications.map((n: any) => ({
        id: n.id,
        titre: n.title || n.titre || '',
        message: n.content || n.message || '',
        type: mapBackendType(n.type),
        estLue: n.isRead ?? n.estLue ?? false, // selon le champ backend
        date: formatDate(n.createdAt || n.date || new Date().toISOString()),
    }));

    // Filtre côté client
    if (filter === 'Non lues') {
        return mapped.filter((n) => !n.estLue);
    }
    return mapped;
}

export async function markNotificationAsRead(notificationId: string): Promise<void> {
    // Utiliser le nouvel endpoint dédié PATCH /notifications/{id}/read
    const response = await apiClient.patch<ApiFeedback<null>>(
        `/notifications/${notificationId}/read`
    );
    unwrapApiData(response.data, 'Erreur lors du marquage de la notification.');
}
