// services/usersService.ts
import { User, UserType } from '../types';
import { UserFormValues, UserFormType } from '../types/userForm.types';
import apiClient from "@/services/api/client";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

// Convertit les données API en objet User
function mapApiToUser(apiData: any, fallbackType?: UserType): User {
    const fullName =
        apiData.fullName ??
        apiData.name ??
        (apiData.firstName && apiData.lastName
            ? `${apiData.firstName} ${apiData.lastName}`
            : null) ??
        (apiData.user?.fullName ?? apiData.user?.firstName + ' ' + apiData.user?.lastName) ??
        '';

    const email = apiData.email ?? apiData.user?.email ?? '';
    const id = apiData.id ?? apiData.user?.id ?? '';

    const type: UserType =
        apiData.type ??
        (apiData.role === 'ROLE_CLINICIAN' || apiData.professionalType ? 'Professional' : null) ??
        (apiData.role === 'ROLE_PATIENT' || apiData.user?.role === 'ROLE_PATIENT' ? 'Patient' : null) ??
        (apiData.role === 'ROLE_ADMIN' || apiData.role === 'ROLE_ROOT' || apiData.user?.role === 'ROLE_ADMIN' ? 'Administrator' : null) ??
        fallbackType ??
        'Patient';

    const statut = apiData.active === false ? 'Inactive' : 'Active';
    const derniereConnexion = apiData.lastLogin ?? apiData.derniereConnexion ?? '';

    return {
        id,
        nom: fullName,
        email,
        type,
        organisation: apiData.organizationName ?? apiData.organisation ?? null,
        statut,
        derniereConnexion,
    };
}

/**
 * Récupère la liste des utilisateurs selon le filtre.
 */
export async function fetchUsers(
    filter: 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs'
): Promise<User[]> {
    try {
        let users: User[] = [];

        switch (filter) {
            case 'Professionnels': {
                const response = await apiClient.get<ApiFeedback<any[]>>('/professionals');
                const data = response.data.data ?? [];
                users = data.map((item) => mapApiToUser(item, 'Professional'));
                break;
            }
            case 'Patients': {
                const response = await apiClient.get<ApiFeedback<any[]>>('/patients');
                const data = response.data.data ?? [];
                users = data.map((item) => mapApiToUser(item, 'Patient'));
                break;
            }
            case 'Administrateurs': {
                const response = await apiClient.get<ApiFeedback<any[]>>('/users');
                const data = response.data.data ?? [];
                users = data
                    .filter((item) => item.role === 'ROLE_ADMIN' || item.role === 'ROLE_ROOT')
                    .map((item) => mapApiToUser(item, 'Administrator'));
                break;
            }
            case 'Tous':
            default: {
                const response = await apiClient.get<ApiFeedback<any[]>>('/users');
                const data = response.data.data ?? [];
                users = data.map((item) => mapApiToUser(item));
                break;
            }
        }

        return users;
    } catch (error) {
        console.error('Erreur fetchUsers:', error);
        throw error;
    }
}

/**
 * Crée un utilisateur professionnel ou patient.
 */
export async function createUser(type: UserFormType, payload: UserFormValues): Promise<void> {
    try {
        switch (type) {
            case 'professional': {
                const formData = new FormData();
                Object.entries(payload).forEach(([key, value]) => {
                    if (value !== undefined && value !== null) {
                        if (value instanceof File) {
                            formData.append(key, value);
                        } else if (typeof value === 'object') {
                            formData.append(key, JSON.stringify(value));
                        } else {
                            formData.append(key, String(value));
                        }
                    }
                });
                const response = await apiClient.post<ApiFeedback<unknown>>('/professionals', formData, {
                    headers: { 'Content-Type': undefined } as any,
                });
                if (response.data.error) throw new Error(response.data.message);
                break;
            }
            case 'patient': {
                const response = await apiClient.post<ApiFeedback<unknown>>('/users', payload);
                if (response.data.error) throw new Error(response.data.message);
                break;
            }
            default:
                throw new Error('Type utilisateur non supporté');
        }
    } catch (error) {
        console.error('Erreur createUser:', error);
        throw error;
    }
}

/**
 * Met à jour un professionnel ou patient.
 */
export async function updateUser(type: UserFormType, userId: string, payload: UserFormValues): Promise<void> {
    try {
        switch (type) {
            case 'professional': {
                const formData = new FormData();
                Object.entries(payload).forEach(([key, value]) => {
                    if (value !== undefined && value !== null) {
                        if (value instanceof File) {
                            formData.append(key, value);
                        } else if (typeof value === 'object') {
                            formData.append(key, JSON.stringify(value));
                        } else {
                            formData.append(key, String(value));
                        }
                    }
                });
                const response = await apiClient.put<ApiFeedback<unknown>>(
                    `/professionals/${userId}`,
                    formData,
                    { headers: { 'Content-Type': undefined } as any }
                );
                if (response.data.error) throw new Error(response.data.message);
                break;
            }
            case 'patient': {
                const formData = new FormData();
                Object.entries(payload).forEach(([key, value]) => {
                    if (value !== undefined && value !== null) {
                        if (value instanceof File) {
                            formData.append(key, value);
                        } else if (typeof value === 'object') {
                            formData.append(key, JSON.stringify(value));
                        } else {
                            formData.append(key, String(value));
                        }
                    }
                });
                const response = await apiClient.put<ApiFeedback<unknown>>(
                    `/patients/${userId}/profile`,
                    formData,
                    { headers: { 'Content-Type': undefined } as any }
                );
                if (response.data.error) throw new Error(response.data.message);
                break;
            }
            default:
                throw new Error('Type utilisateur non supporté');
        }
    } catch (error) {
        console.error('Erreur updateUser:', error);
        throw error;
    }
}
