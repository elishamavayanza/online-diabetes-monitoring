export interface SuspensionPayload {
    reason: string;
    durationDays?: number;
    startsAt?: string;
    endsAt?: string;
}

export const SUSPENSION_CODES = ['account_suspended', 'organization_suspended'] as const;

export type SuspensionCode = (typeof SUSPENSION_CODES)[number];

export interface SuspensionBlockInfo {
    code?: SuspensionCode;
    title: string;
    message: string;
    reason?: string;
    endsAt?: string;
}