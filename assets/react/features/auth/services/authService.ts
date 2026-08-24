import apiClient from '@/services/api/client';
import { tokenStorage } from '@/services/storage/storage.service';
import { AuthResponse, LoginPayload } from '../types/auth.types';
import { UserRole } from '@/react/app/layouts/MainLayout/components/Sidebar/sidebar.config';

/**
 * Mappe les rôles Symfony (ROLE_*) vers les rôles applicatifs.
 */
function mapSymfonyRoleToUserRole(roles: string[]): UserRole {
    if (roles.includes('ROLE_ROOT')) return 'ROOT';
    if (roles.includes('ROLE_ADMIN')) return 'ADMIN';
    if (roles.includes('ROLE_CLINICIAN')) return 'CLINICIAN';
    if (roles.includes('ROLE_NUTRITIONIST')) return 'NUTRITIONIST';
    if (roles.includes('ROLE_PATIENT')) return 'PATIENT';
    return 'PATIENT';
}

export async function login(payload: LoginPayload): Promise<AuthResponse> {
    // Appel réel à l'API Symfony
    const response = await apiClient.post<{
        token: string;
        fullName?: string;
        email?: string;
        roles?: string[];
        permissions?: string[];
        id?: string;
    }>('/login_check', {
        username: payload.emailOrUsername,   // le backend attend "username"
        password: payload.password,
    });

    const data = response.data;

    const user = {
        id: data.id ?? 'unknown',
        name: data.fullName ?? payload.emailOrUsername,
        email: data.email ?? payload.emailOrUsername,
        permissions: data.permissions ?? [],
        role: mapSymfonyRoleToUserRole(data.roles ?? []),
        photoUrl: undefined,
    };

    // Stocke le token JWT pour les prochaines requêtes
    tokenStorage.setAccessToken(data.token);

    return {
        token: data.token,
        user,
    };
}
