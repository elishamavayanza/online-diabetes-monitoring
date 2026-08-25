import { AuditLog, DataAccessLog } from '../types';

export async function fetchAuditLogs(): Promise<AuditLog[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return [
        {
            id: '1',
            utilisateur: 'Admin Principal',
            action: 'CONNEXION',
            ressource: 'Authentification',
            date: '2026-08-25 08:00',
            adresseIp: '192.168.1.10',
            resultat: 'SUCCESS',
        },
        {
            id: '2',
            utilisateur: 'Dr. Jean Mukendi',
            action: 'LECTURE',
            ressource: 'Dossier patient',
            date: '2026-08-25 08:30',
            adresseIp: '192.168.1.22',
            resultat: 'SUCCESS',
        },
        {
            id: '3',
            utilisateur: 'Admin Hôpital B',
            action: 'MODIFICATION',
            ressource: 'Établissement',
            date: '2026-08-24 15:45',
            adresseIp: '192.168.1.30',
            resultat: 'FAILURE',
        },
    ];
}

export async function fetchDataAccessLogs(): Promise<DataAccessLog[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return [
        {
            id: '1',
            utilisateur: 'Dr. Jean Mukendi',
            patient: 'Marie Zawadi',
            ressourceConsultee: 'Résultats de glycémie',
            motif: 'Consultation médicale',
            date: '2026-08-25 09:15',
        },
        {
            id: '2',
            utilisateur: 'Nutritionniste Sarah',
            patient: 'Marie Zawadi',
            ressourceConsultee: 'Plan alimentaire',
            motif: 'Suivi nutritionnel',
            date: '2026-08-24 14:00',
        },
        {
            id: '3',
            utilisateur: 'Admin Hôpital B',
            patient: 'Patient Test',
            ressourceConsultee: 'Dossier administratif',
            motif: 'Gestion des comptes',
            date: '2026-08-23 11:30',
        },
    ];
}
