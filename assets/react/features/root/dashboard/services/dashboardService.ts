// services/dashboardService.ts
import { DashboardData } from '../types';
import apiClient from "@/services/api/client";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

// Fonction utilitaire pour extraire un tableau de données depuis une réponse API
function extractArray(payload: any): any[] {
    if (Array.isArray(payload)) return payload;
    if (payload && typeof payload === 'object') {
        // Chercher dans les propriétés connues
        const possibleKeys = ['data', 'items', 'organizations', 'users', 'patients', 'professionals', 'results'];
        for (const key of possibleKeys) {
            if (Array.isArray(payload[key])) return payload[key];
        }
        // Si l'objet lui-même semble être une entité unique, on le met dans un tableau
        if (payload.id && (payload.name || payload.email || payload.fullName)) {
            return [payload];
        }
    }
    return [];
}

export async function fetchDashboardData(): Promise<DashboardData> {
    try {
        // Appels parallèles
        const [orgsResponse, usersResponse, patientsResponse, professionalsResponse] = await Promise.all([
            apiClient.get<ApiFeedback<any>>('/healthcare-organizations', { params: { limit: 100 } }),
            apiClient.get<ApiFeedback<any>>('/users', { params: { limit: 100 } }),
            apiClient.get<ApiFeedback<any>>('/patients', { params: { limit: 100 } }),
            apiClient.get<ApiFeedback<any>>('/professionals', { params: { limit: 100 } }),
        ]);

        // Extraction robuste
        const orgs = extractArray(orgsResponse.data.data ?? orgsResponse.data);
        const users = extractArray(usersResponse.data.data ?? usersResponse.data);
        const patients = extractArray(patientsResponse.data.data ?? patientsResponse.data);
        const professionals = extractArray(professionalsResponse.data.data ?? professionalsResponse.data);

        // Calcul des stats
        const totalOrgs = orgs.length;
        const totalUsers = users.length;
        const totalProfessionals = professionals.length;
        const totalPatients = patients.length;
        const activeOrgs = orgs.filter((org: any) => org.active === true || org.isActive === true).length;

        const stats = [
            { id: 'orgs', label: 'Organisations', value: totalOrgs },
            { id: 'users', label: 'Utilisateurs', value: totalUsers },
            { id: 'professionals', label: 'Professionnels', value: totalProfessionals },
            { id: 'patients', label: 'Patients', value: totalPatients },
            { id: 'active-orgs', label: 'Organisations actives', value: activeOrgs },
        ];

        // TODO: Remplacer par un vrai endpoint quand disponible
        const recentActivities: any[] = [];

        const platformStatus = [
            { id: 'users-active', label: 'Utilisateurs actifs', isActive: true },
            { id: 'orgs-active', label: 'Organisations actives', isActive: activeOrgs > 0 },
            { id: 'notifications', label: 'Notifications', isActive: true },
            { id: 'services', label: 'Services', isActive: true },
        ];

        return {
            stats,
            recentActivities,
            platformStatus,
        };
    } catch (error) {
        console.error('Erreur fetchDashboardData:', error);
        throw error;
    }
}
