import apiClient from "@/services/api/client";
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import {
    CreateExternalFollowPayload,
    ExternalFollowInvitation,
    ExternalFollowLogEntry,
    RenewExternalFollowPayload,
} from '../types/types';

export function getOrganizationIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        const orgs = payload?.organizations;
        if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
            return String(orgs[0].organization_id);
        }
    } catch (e) {
        console.error('Erreur décodage token:', e);
    }
    return null;
}

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

export async function fetchExternalFollows(organizationId: string): Promise<ExternalFollowInvitation[]> {
    const response = await apiClient.get<ApiFeedback<ExternalFollowInvitation[]>>(
        `/healthcare-organizations/${organizationId}/external-follows`
    );
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la récupération des invitations');
    return response.data.data ?? [];
}

export async function createExternalFollow(
    organizationId: string,
    payload: CreateExternalFollowPayload
): Promise<ExternalFollowInvitation> {
    const response = await apiClient.post<ApiFeedback<ExternalFollowInvitation>>(
        `/healthcare-organizations/${organizationId}/external-follows`,
        payload
    );
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la création de l’invitation');
    return response.data.data;
}

export async function renewExternalFollow(
    organizationId: string,
    invitationId: string,
    payload: RenewExternalFollowPayload
): Promise<ExternalFollowInvitation> {
    const response = await apiClient.post<ApiFeedback<ExternalFollowInvitation>>(
        `/healthcare-organizations/${organizationId}/external-follows/${invitationId}/renew`,
        payload
    );
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors du renouvellement');
    return response.data.data;
}

export async function revokeExternalFollow(
    organizationId: string,
    invitationId: string
): Promise<ExternalFollowInvitation> {
    const response = await apiClient.post<ApiFeedback<ExternalFollowInvitation>>(
        `/healthcare-organizations/${organizationId}/external-follows/${invitationId}/revoke`
    );
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la coupure de l’accès');
    return response.data.data;
}

export async function fetchExternalFollowLogs(
    organizationId: string,
    invitationId?: string
): Promise<ExternalFollowLogEntry[]> {
    const config = invitationId
        ? { params: { invitationId } }
        : undefined;
    const response = await apiClient.get<ApiFeedback<ExternalFollowLogEntry[]>>(
        `/healthcare-organizations/${organizationId}/external-follows/logs`,
        config
    );
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la récupération du journal');
    return response.data.data ?? [];
}

export async function fetchInvitationByToken(token: string): Promise<ExternalFollowInvitation> {
    const response = await apiClient.get<ApiFeedback<ExternalFollowInvitation>>(
        `/external-follows/invitations/${token}`
    );
    if (response.data.error) throw new Error(response.data.message || 'Invitation introuvable');
    return response.data.data;
}

export async function acceptInvitation(token: string): Promise<ExternalFollowInvitation> {
    const response = await apiClient.post<ApiFeedback<ExternalFollowInvitation>>(
        `/external-follows/invitations/${token}/accept`
    );
    if (response.data.error) throw new Error(response.data.message || 'Impossible d’accepter l’invitation');
    return response.data.data;
}

export async function declineInvitation(token: string): Promise<ExternalFollowInvitation> {
    const response = await apiClient.post<ApiFeedback<ExternalFollowInvitation>>(
        `/external-follows/invitations/${token}/decline`
    );
    if (response.data.error) throw new Error(response.data.message || 'Impossible de refuser l’invitation');
    return response.data.data;
}

export async function fetchMyExternalFollows(): Promise<ExternalFollowInvitation[]> {
    const response = await apiClient.get<ApiFeedback<ExternalFollowInvitation[]>>('/external-follows/my');
    if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la récupération de vos suivis');
    return response.data.data ?? [];
}