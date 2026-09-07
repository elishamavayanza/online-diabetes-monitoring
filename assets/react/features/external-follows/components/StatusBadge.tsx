import React from 'react';
import { Badge } from '@/react/components/UI/Badge';
import type { BadgeVariant } from '@/react/hook-components/UI/Badge';
import { ExternalFollowStatus, EXTERNAL_FOLLOW_STATUS_LABELS } from '../types/types';

const STATUS_VARIANT: Record<ExternalFollowStatus, BadgeVariant> = {
    PENDING: 'warning',
    ACCEPTED: 'success',
    DECLINED: 'default',
    REVOKED: 'error',
    CLOSED_BY_PROFESSIONAL: 'info',
    EXPIRED: 'default',
};

export function StatusBadge({ status }: { status: ExternalFollowStatus }) {
    return (
        <Badge variant={STATUS_VARIANT[status]}>
            {EXTERNAL_FOLLOW_STATUS_LABELS[status] ?? status}
        </Badge>
    );
}