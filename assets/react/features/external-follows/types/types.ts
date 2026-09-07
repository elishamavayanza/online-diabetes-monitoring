export type ExternalFollowStatus = 'PENDING' | 'ACCEPTED' | 'DECLINED' | 'REVOKED' | 'EXPIRED';

export interface ExternalFollowInvitation {
    id: string;
    patientId: string;
    patientName: string;
    patientPhotoUrl?: string | null;
    email: string;
    professionalId: string;
    professionalName: string;
    organizationId: string;
    organizationName: string;
    status: ExternalFollowStatus;
    startDate: string | null;
    endDate: string | null;
    message: string | null;
    invitedByName: string;
    createdAt: string;
    acceptedAt: string | null;
    revokedAt: string | null;
}

export interface ExternalFollowLogEntry {
    id: string;
    patientId: string;
    professionalName: string;
    action: string;
    actionLabel: string | null;
    detail: string | null;
    createdAt: string;
}

export interface CreateExternalFollowPayload {
    patientId: number;
    email: string;
    durationDays: number;
    message?: string | null;
}

export interface RenewExternalFollowPayload {
    durationDays: number;
}

export const EXTERNAL_FOLLOW_DURATION_PRESETS = [30, 90, 180, 365] as const;

export const EXTERNAL_FOLLOW_STATUS_LABELS: Record<ExternalFollowStatus, string> = {
    PENDING: 'En attente',
    ACCEPTED: 'Acceptée',
    DECLINED: 'Refusée',
    REVOKED: 'Coupée',
    EXPIRED: 'Expirée',
};