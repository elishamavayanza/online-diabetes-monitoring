// services/profileService.ts
import apiClient from "@/services/api/client";
import { UserProfileData, ProfileUpdatePayload } from '../types';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

interface JwtPayload {
    sub?: string | number;
    id?: string | number;
    uid?: string | number;
    user_id?: string | number;
    fullName?: string;
    username?: string;
    email?: string;
    phone?: string;
    photoUrl?: string;
    locale?: string;
    role?: string;
    roles?: string[];
    [key: string]: any;
}

// Fonction pour extraire l'ID utilisateur du token JWT
function getUserIdFromToken(payload: JwtPayload | null): string {
    if (!payload) return '';
    const raw = payload.sub ?? payload.id ?? payload.uid ?? payload.user_id;
    return raw ? String(raw) : '';
}

/**
 * Récupère le profil de l'utilisateur connecté.
 * @param providedUserId - ID fourni par le contexte (optionnel, prioritaire)
 */
export async function fetchUserProfile(providedUserId?: string): Promise<UserProfileData> {
    const token = tokenStorage.getAccessToken();
    if (!token) throw new Error("Non authentifié");

    const payload = decodeJwtPayload(token) as JwtPayload | null;
    if (!payload) throw new Error("Token invalide");

    const userId = providedUserId || getUserIdFromToken(payload);
    if (!userId) throw new Error("Identifiant utilisateur introuvable");

    const roles: string[] = payload.roles ?? (payload.role ? [payload.role] : []);

    if (roles.includes('ROLE_PATIENT')) {
        const response = await apiClient.get<ApiFeedback<any>>(`/patients/${userId}/profile`);
        return mapPatientToProfile(response.data.data, userId, payload);
    } else if (roles.includes('ROLE_CLINICIAN') || roles.includes('ROLE_NUTRITIONIST')) {
        const response = await apiClient.get<ApiFeedback<any>>(`/professionals/${userId}`);
        return mapProfessionalToProfile(response.data.data, userId, payload);
    } else {
        // Admin / Root : pas d'endpoint GET /users/{id}, on utilise les données du token
        return {
            id: userId,
            name: payload.fullName ?? payload.username ?? 'Utilisateur',
            email: payload.email ?? '',
            role: roles[0] ?? 'ROOT',
            phone: payload.phone ?? undefined,
            avatarUrl: payload.photoUrl ?? undefined,
            locale: payload.locale ?? 'fr',
        };
    }
}

/**
 * Met à jour le profil de l'utilisateur connecté.
 * @param payload Données du formulaire
 * @param avatarFile Fichier avatar optionnel
 * @param providedUserId ID fourni par le contexte (optionnel, prioritaire)
 */
export async function updateUserProfile(
    payload: ProfileUpdatePayload,
    avatarFile?: File | null,
    providedUserId?: string
): Promise<UserProfileData> {
    const token = tokenStorage.getAccessToken();
    if (!token) throw new Error("Non authentifié");

    const decoded = decodeJwtPayload(token) as JwtPayload | null;
    if (!decoded) throw new Error("Token invalide");

    const userId = providedUserId || getUserIdFromToken(decoded);
    if (!userId) throw new Error("Identifiant utilisateur introuvable dans le token.");

    const roles: string[] = decoded.roles ?? (decoded.role ? [decoded.role] : []);

    if (roles.includes('ROLE_PATIENT')) {
        const formData = buildFormData(payload, avatarFile);
        const response = await apiClient.put<ApiFeedback<any>>(
            `/patients/${userId}/profile`,
            formData,
            { headers: { 'Content-Type': undefined } as any }
        );
        return mapPatientToProfile(response.data.data, userId, decoded);
    } else if (roles.includes('ROLE_CLINICIAN') || roles.includes('ROLE_NUTRITIONIST')) {
        const formData = buildFormData(payload, avatarFile);
        const response = await apiClient.put<ApiFeedback<any>>(
            `/professionals/${userId}`,
            formData,
            { headers: { 'Content-Type': undefined } as any }
        );
        return mapProfessionalToProfile(response.data.data, userId, decoded);
    } else {
        // Admin / Root : endpoint PUT /users/{id} (JSON)
        const body = {
            fullName: payload.name,
            phone: payload.phone,
            avatarUrl: payload.avatarUrl,
        };
        const response = await apiClient.put<ApiFeedback<any>>(`/users/${userId}`, body);
        return mapUserToProfile(response.data.data, userId, decoded);
    }
}

/**
 * Change le mot de passe de l'utilisateur connecté.
 */
export async function changePassword(data: {
    oldPassword: string;
    newPassword: string;
    confirmPassword: string;
}): Promise<void> {
    const response = await apiClient.post<ApiFeedback<unknown>>('/change-password', data);
    if (response.data.error) {
        throw new Error(response.data.message || 'Erreur lors du changement de mot de passe.');
    }
}

// --- Fonctions utilitaires ---

function buildFormData(payload: ProfileUpdatePayload, avatarFile?: File | null): FormData {
    const formData = new FormData();
    formData.append('fullName', payload.name);
    if (payload.phone) formData.append('phone', payload.phone);
    if (payload.avatarUrl) formData.append('avatarUrl', payload.avatarUrl);
    if (avatarFile) {
        formData.append('avatarFile', avatarFile);
    }
    return formData;
}

function mapPatientToProfile(data: any, userId: string, decoded: JwtPayload): UserProfileData {
    return {
        id: userId,
        name: data.fullName ?? decoded.fullName ?? '',
        email: data.email ?? decoded.email ?? '',
        role: 'ROLE_PATIENT',
        phone: data.phone ?? undefined,
        avatarUrl: data.avatarUrl ?? data.photoUrl ?? undefined,
        locale: data.locale ?? 'fr',
    };
}

function mapProfessionalToProfile(data: any, userId: string, decoded: JwtPayload): UserProfileData {
    return {
        id: userId,
        name: data.fullName ?? decoded.fullName ?? '',
        email: data.email ?? decoded.email ?? '',
        role: decoded.roles?.[0] ?? 'ROLE_CLINICIAN',
        phone: data.phone ?? undefined,
        avatarUrl: data.avatarUrl ?? data.photoUrl ?? undefined,
        locale: data.locale ?? 'fr',
    };
}

function mapUserToProfile(data: any, userId: string, decoded: JwtPayload): UserProfileData {
    return {
        id: userId,
        name: data.fullName ?? decoded.fullName ?? '',
        email: data.email ?? decoded.email ?? '',
        role: decoded.roles?.[0] ?? 'ROOT',
        phone: data.phone ?? undefined,
        avatarUrl: data.avatarUrl ?? data.photoUrl ?? undefined,
        locale: data.locale ?? 'fr',
    };
}
