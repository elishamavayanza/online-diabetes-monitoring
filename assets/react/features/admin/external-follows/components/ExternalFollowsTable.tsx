import { getDateFormatLocale } from '@/react/i18n/dateLocale';
import React, { useState } from 'react';
import { Button } from '@/react/components/UI/Button';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { ExternalFollowInvitation } from '../types/types';
import { StatusBadge } from './StatusBadge';
import { useRevokeExternalFollow } from '../hooks/useRevokeExternalFollow';
import { useI18n } from '@/react/i18n/I18nContext';

interface ExternalFollowsTableProps {
    invitations: ExternalFollowInvitation[];
    organizationId: string;
    isLoading: boolean;
    error: string | null;
    onRefresh: () => void;
    onRenew: (invitation: ExternalFollowInvitation) => void;
    onOpenLogs: (invitation: ExternalFollowInvitation) => void;
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString(getDateFormatLocale(), {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

export function ExternalFollowsTable({
                                         invitations,
                                         organizationId,
                                         isLoading,
                                         error,
                                         onRefresh,
                                         onRenew,
                                         onOpenLogs,
                                     }: ExternalFollowsTableProps) {
    const { submit: revoke, isSubmitting: isRevoking } = useRevokeExternalFollow(organizationId, { onSuccess: onRefresh });
    const [revokeTarget, setRevokeTarget] = useState<ExternalFollowInvitation | null>(null);
    const { t } = useI18n();

    const handleRevokeConfirm = async () => {
        if (!revokeTarget) return;
        await revoke(revokeTarget.id);
        setRevokeTarget(null);
    };

    // Définition des colonnes pour la DataTable
    const columns = [
        { key: 'patientName', title: t('Patient') },
        {
            key: 'professionalName',
            title: t('Professionnel'),
            render: (inv: ExternalFollowInvitation) => (
                <>
                    {inv.professionalName}
                    <span className="external-follows-table__email">{inv.email}</span>
                </>
            ),
        },
        {
            key: 'status',
            title: t('Statut'),
            render: (inv: ExternalFollowInvitation) => (
                <>
                    <StatusBadge status={inv.status} />
                    {inv.status === 'CLOSED_BY_PROFESSIONAL' && inv.closureReason && (
                        <span className="external-follows-table__closure-reason" title={inv.closureReason}>
                            {t('Motif :')} {inv.closureReason}
                        </span>
                    )}
                </>
            ),
        },
        {
            key: 'startDate',
            title: t('Début'),
            render: (inv: ExternalFollowInvitation) => formatDate(inv.startDate),
        },
        {
            key: 'endDate',
            title: t('Fin'),
            render: (inv: ExternalFollowInvitation) => formatDate(inv.endDate),
        },
        {
            key: 'invitedByName',
            title: t('Invité par'),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (inv: ExternalFollowInvitation) => {
                const isActive = inv.status === 'ACCEPTED' || inv.status === 'PENDING';
                const isExpired = inv.status === 'EXPIRED';
                return (
                    <div className="external-follows-table__actions">
                        <Button
                            variant="ghost"
                            size="small"
                            onClick={() => onOpenLogs(inv)}
                            title={t("Voir le journal d\u2019activité")}
                        >
                            {t('Journal')}
                        </Button>
                        {isActive && (
                            <Button variant="ghost" size="small" onClick={() => onRenew(inv)}>
                                {t('Renouveler')}
                            </Button>
                        )}
                        {isActive && (
                            <Button
                                variant="ghost"
                                size="small"
                                className="external-follows-table__revoke"
                                onClick={() => setRevokeTarget(inv)}
                            >
                                {t("Couper l\u2019accès")}
                            </Button>
                        )}
                        {isExpired && (
                            <span className="external-follows-table__expired-note">{t('Délai écoulé')}</span>
                        )}
                    </div>
                );
            },
        },
    ];

    // Contenu principal
    let content: React.ReactNode;

    if (isLoading) {
        content = (
            <div className="external-follows-table__empty">
                <Spinner size="medium" />
            </div>
        );
    } else if (error) {
        content = (
            <div className="external-follows-table__empty">
                <Alert variant="error">{error}</Alert>
            </div>
        );
    } else if (invitations.length === 0) {
        content = (
            <div className="external-follows-table__empty">
                <p>{t('Aucune invitation pour le moment.')}</p>
                <p className="external-follows-table__empty-hint">
                    {t("Utilisez « Nouvelle invitation » pour partager le suivi d\u2019un patient.")}
                </p>
            </div>
        );
    } else {
        content = <DataTable columns={columns} data={invitations} />;
    }

    return (
        <>
            <Card className="external-follows-table-card" fullWidth>
                {content}
            </Card>
            <ConfirmDialog
                isOpen={revokeTarget !== null}
                onClose={() => setRevokeTarget(null)}
                onConfirm={handleRevokeConfirm}
                title={t("Couper l\u2019accès")}
                message={
                    revokeTarget
                        ? t("L\u2019accès de {{ professional }} au dossier de {{ patient }} sera coupé immédiatement. Cette action est irréversible.", { professional: revokeTarget.professionalName, patient: revokeTarget.patientName })
                        : ''
                }
                confirmLabel={isRevoking ? t('Coupure...') : t("Couper l\u2019accès")}
                cancelLabel={t('Annuler')}
            />
        </>
    );
}
