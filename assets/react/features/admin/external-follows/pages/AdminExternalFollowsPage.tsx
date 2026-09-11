import React, { useState } from 'react';
import '@/styles/pages/admin/external-follows/_external-follows.scss';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Badge } from '@/react/components/UI/Badge';
import { useExternalFollows } from '../hooks/useExternalFollows';
import { ExternalFollowInvitation } from '../types/types';
import { ExternalFollowsTable } from '../components/ExternalFollowsTable';
import { CreateInvitationModal } from '../components/CreateInvitationModal';
import { RenewModal } from '../components/RenewModal';
import { LogsModal } from '../components/LogsModal';
import { getOrganizationIdFromToken } from '../services/externalFollowsService';
import { useI18n } from '@/react/i18n/I18nContext';

export function AdminExternalFollowsPage() {
    const { user } = useAuth();
    const { t } = useI18n();
    const [organizationId] = useState<string | null>(() => getOrganizationIdFromToken());
    const { invitations, isLoading, error, refetch } = useExternalFollows(organizationId);

    const [isCreateOpen, setCreateOpen] = useState(false);
    const [renewTarget, setRenewTarget] = useState<ExternalFollowInvitation | null>(null);
    const [logsTarget, setLogsTarget] = useState<ExternalFollowInvitation | null>(null);

    if (user?.role !== 'ADMIN') {
        return (
            <div className="admin-external-follows-page">
                <Alert variant="error">
                    {t("Accès réservé aux administrateurs d'organisation.")}
                </Alert>
            </div>
        );
    }

    if (!organizationId) {
        return (
            <div className="admin-external-follows-page">
                <Alert variant="error">{t('Organisation introuvable dans votre session.')}</Alert>
            </div>
        );
    }

    return (
        <div className="admin-external-follows-page">
            <div className="admin-external-follows-page__header">
                <div>
                    <h1>{t('Suivi externe (hors organisation)')}</h1>
                    <p className="admin-external-follows-page__subtitle">
                        {t("Partagez le suivi d'un patient avec un professionnel d'une autre organisation pour une durée définie.")}
                    </p>
                </div>
                <div className="admin-external-follows-page__header-actions">
                    <Badge variant="primary" size="medium">
                        {invitations.length} invitation{invitations.length > 1 ? 's' : ''}
                    </Badge>
                    <Button onClick={() => setCreateOpen(true)}>{t('Nouvelle invitation')}</Button>
                </div>
            </div>

            {isLoading && <Spinner size="medium" />}
            {!isLoading && error && <Alert variant="error">{error}</Alert>}
            {!isLoading && !error && (
                <ExternalFollowsTable
                    invitations={invitations}
                    organizationId={organizationId}
                    isLoading={false}
                    error={null}
                    onRefresh={refetch}
                    onRenew={(inv) => setRenewTarget(inv)}
                    onOpenLogs={(inv) => setLogsTarget(inv)}
                />
            )}

            <CreateInvitationModal
                isOpen={isCreateOpen}
                organizationId={organizationId}
                onClose={() => setCreateOpen(false)}
                onSuccess={refetch}
            />

            {renewTarget && (
                <RenewModal
                    isOpen
                    organizationId={organizationId}
                    invitationId={renewTarget.id}
                    patientName={renewTarget.patientName}
                    onClose={() => setRenewTarget(null)}
                    onSuccess={refetch}
                />
            )}

            {logsTarget && (
                <LogsModal
                    isOpen
                    organizationId={organizationId}
                    invitationId={logsTarget.id}
                    patientName={logsTarget.patientName}
                    professionalName={logsTarget.professionalName}
                    onClose={() => setLogsTarget(null)}
                />
            )}
        </div>
    );
}