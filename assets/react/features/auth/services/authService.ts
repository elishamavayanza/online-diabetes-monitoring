// assets/react/features/auth/services/authService.ts
import apiClient from '@/services/api/client';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
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
        photoUrl?: string; // au cas où le backend renverrait aussi l'URL
    }>('/login_check', {
        username: payload.emailOrUsername,   // le backend attend "username"
        password: payload.password,
    });

    const { token } = response.data;
    tokenStorage.setAccessToken(token);

    // Décode le token JWT pour récupérer les claims personnalisés
    const decoded = decodeJwtPayload(token);
    if (!decoded) {
        throw new Error('Token JWT invalide');
    }

    const baseUrl = (import.meta as unknown as { env: { VITE_API_BASE_URL?: string } }).env.VITE_API_BASE_URL || '';

    const user = {
        id: decoded.sub ?? 'unknown',
        name: decoded.fullName ?? payload.emailOrUsername,
        email: decoded.email ?? payload.emailOrUsername,
        permissions: decoded.permissions ?? [],
        role: (decoded.role as UserRole) ?? mapSymfonyRoleToUserRole(decoded.roles ?? []),
        photoUrl: decoded.photoUrl
            ? decoded.photoUrl.startsWith('http')
                ? decoded.photoUrl
                : `${baseUrl}${decoded.photoUrl}` // préfixe si URL relative
            : undefined,
    };

    return {
        token,
        user,
    };
}
