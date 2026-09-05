// assets/react/features/auth/services/authService.ts
import apiClient from '@/services/api/client';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import { AuthResponse, LoginPayload } from '../types/auth.types';
import { UserRole } from '@/react/app/layouts/MainLayout/components/Sidebar/sidebar.config';
import { resolveAvatarUrl } from '@/react/utils/avatarUrl';

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
        refresh_token?: string;
    }>('/login_check', {
        username: payload.emailOrUsername,   // le backend attend "username"
        password: payload.password,
    });

    const { token } = response.data;
    tokenStorage.setAccessToken(token);
    if (response.data.refresh_token) {
        tokenStorage.setRefreshToken(response.data.refresh_token);
    }

    // Décode le token JWT pour récupérer les claims personnalisés
    const decoded = decodeJwtPayload(token);
    if (!decoded) {
        throw new Error('Token JWT invalide');
    }

    const user = {
        id: decoded.sub ?? 'unknown',
        name: decoded.fullName ?? payload.emailOrUsername,
        email: decoded.email ?? payload.emailOrUsername,
        permissions: decoded.permissions ?? [],
        role: (decoded.role as UserRole) ?? mapSymfonyRoleToUserRole(decoded.roles ?? []),
        photoUrl: resolveAvatarUrl(decoded.photoUrl ?? decoded.avatarUrl),
    };

    return {
        token,
        user,
    };
}
