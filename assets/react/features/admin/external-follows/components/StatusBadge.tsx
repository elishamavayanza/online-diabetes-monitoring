import React from 'react';
import { Badge } from '@/react/components/UI/Badge';
import type { BadgeVariant } from '@/react/hook-components/UI/Badge';
import { ExternalFollowStatus, EXTERNAL_FOLLOW_STATUS_LABELS } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

const STATUS_VARIANT: Record<ExternalFollowStatus, BadgeVariant> = {
    PENDING: 'warning',
    ACCEPTED: 'success',
    DECLINED: 'default',
    REVOKED: 'error',
    CLOSED_BY_PROFESSIONAL: 'info',
    EXPIRED: 'default',
};

export function StatusBadge({ status }: { status: ExternalFollowStatus }) {
    const { t } = useI18n();

    return (
        <Badge variant={STATUS_VARIANT[status]}>
            {t(EXTERNAL_FOLLOW_STATUS_LABELS[status] ?? status)}
        </Badge>
    );
}