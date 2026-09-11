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

export interface PaginatedList<T> {
    items: T[];
    total: number;
    page: number;
    limit: number;
    totalPages: number;
}

export interface UsersQuery {
    tab?: 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs';
    q?: string;
    page?: number;
    limit?: number;
    sort?: string;
    order?: 'asc' | 'desc';
    org?: string;
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

    const roles: string[] = Array.isArray(apiData.roles)
        ? apiData.roles
        : (apiData.role ? [apiData.role] : []);
    const hasRole = (role: string) =>
        roles.includes(role) || apiData.user?.role === role;

    const type: UserType =
        apiData.type ??
        (hasRole('ROLE_CLINICIAN') || hasRole('ROLE_NUTRITIONIST') || apiData.professionalType
            ? 'Professional'
            : null) ??
        (hasRole('ROLE_PATIENT') ? 'Patient' : null) ??
        (hasRole('ROLE_ADMIN') || hasRole('ROLE_ROOT') ? 'Administrator' : null) ??
        fallbackType ??
        'Patient';

    const rawStatus =
        apiData.status ??
        (apiData.active === false ? 'INACTIVE' : 'ACTIVE');
    const statut =
        rawStatus === 'SUSPENDED'
            ? 'Inactive'
            : rawStatus === 'PENDING_ACTIVATION'
                ? 'Pending'
                : 'Active';
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
 * Récupère la liste paginée des utilisateurs avec recherche / tri serveur.
 */
export async function fetchPaginatedUsers(query: UsersQuery = {}): Promise<PaginatedList<User>> {
    const { tab = 'Tous', q, page = 1, limit = 10, sort, order = 'desc', org } = query;

    const params: Record<string, string | number | undefined> = {
        page,
        limit,
        q: q || undefined,
        sort,
        order,
        org: org || undefined,
    };

    let endpoint = '/users';
    let fallbackType: UserType = 'Patient';

    if (tab === 'Professionnels') {
        endpoint = '/professionals';
        fallbackType = 'Professional';
    } else if (tab === 'Patients') {
        endpoint = '/patients';
        fallbackType = 'Patient';
    } else if (tab === 'Administrateurs') {
        params.role = 'admin';
    }

    const response = await apiClient.get<ApiFeedback<PaginatedList<any>>>(endpoint, { params });

    if (response.data.error) {
        throw new Error(response.data.message || 'Erreur lors du chargement des utilisateurs.');
    }

    const payload = response.data.data;

    // Fallback : si le serveur renvoie un simple tableau (ancien contrat),
    // on l'utilise tel quel sur la première page.
    const items = Array.isArray(payload) ? payload.slice(0, limit) : (payload?.items ?? []);
    const total = Array.isArray(payload) ? payload.length : (payload?.total ?? items.length);

    return {
        items: items.map((item) => mapApiToUser(item, fallbackType)),
        total,
        page: Array.isArray(payload) ? 1 : (payload?.page ?? page),
        limit: Array.isArray(payload) ? limit : (payload?.limit ?? limit),
        totalPages: Array.isArray(payload)
            ? Math.ceil(total / limit)
            : (payload?.totalPages ?? Math.ceil(total / limit)),
    };
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
