import React, { useCallback, useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '@/react/app/providers/AuthProvider';
import '@/styles/pages/admin/external-follows/_external-follows.scss';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Badge } from '@/react/components/UI/Badge';
import type { BadgeVariant } from '@/react/hook-components/UI/Badge';
import { ExternalFollowInvitation, EXTERNAL_FOLLOW_STATUS_LABELS } from '../types/types';
import { fetchMyExternalFollows } from '../services/externalFollowsService';
import { CloseFollowModal } from '../components/CloseFollowModal';
import { useI18n } from '@/react/i18n/I18nContext';

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

const STATUS_VARIANT: Record<string, BadgeVariant> = {
    ACCEPTED: 'success',
    PENDING: 'warning',
    EXPIRED: 'default',
    REVOKED: 'error',
    DECLINED: 'default',
    CLOSED_BY_PROFESSIONAL: 'info',
};

export function MyExternalFollowsPage() {
    const { user } = useAuth();
    const { t } = useI18n();
    const [follows, setFollows] = useState<ExternalFollowInvitation[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [selectedToClose, setSelectedToClose] = useState<ExternalFollowInvitation | null>(null);

    const refetch = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchMyExternalFollows();
            setFollows(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : t('Erreur lors de la récupération de vos suivis externes.'));
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        void refetch();
    }, [refetch]);

    const rolePrefix = user?.role?.toLowerCase() ?? 'clinician';

    if (isLoading) {
        return (
            <div className="my-external-follows-page">
                <Spinner size="medium" />
            </div>
        );
    }

    if (error) {
        return (
            <div className="my-external-follows-page">
                <Alert variant="error">{error}</Alert>
            </div>
        );
    }

    if (follows.length === 0) {
        return (
            <div className="my-external-follows-page my-external-follows-page--empty">
                <h1>{t('Mes suivis externes')}</h1>
                <p>{t('Vous ne suivez aucun patient en dehors de votre organisation pour le moment.')}</p>
            </div>
        );
    }

    return (
        <div className="my-external-follows-page">
            <div className="my-external-follows-page__header">
                <h1>{t('Mes suivis externes')}</h1>
                <span className="my-external-follows-page__count">{follows.length} patient{follows.length > 1 ? 's' : ''}</span>
            </div>
            <div className="my-external-follows-page__list">
                {follows.map((follow) => (
                    <div key={follow.id} className="my-external-follows-page__item">
                        <div className="my-external-follows-page__item-info">
                            <h3>{follow.patientName}</h3>
                            <p>{t('Organisation :')} {follow.organizationName}</p>
                            <p>{t("Accès jusqu'au :")} {formatDate(follow.endDate)}</p>
                        </div>
                        <div className="my-external-follows-page__item-actions">
                            <Badge variant={STATUS_VARIANT[follow.status] ?? 'default'}>
                                {EXTERNAL_FOLLOW_STATUS_LABELS[follow.status] ?? follow.status}
                            </Badge>
                            {(follow.status === 'ACCEPTED' || follow.status === 'PENDING') && (
                                <>
                                    <Link to={`/${rolePrefix}/patients/${follow.patientId}/record`}>
                                        <Button variant="outline" size="small">{t('Voir le dossier')}</Button>
                                    </Link>
                                    {follow.status === 'ACCEPTED' && (
                                        <Button variant="danger" size="small" onClick={() => setSelectedToClose(follow)}>
                                            {t('Fermer mon suivi')}
                                        </Button>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                ))}
            </div>

            {selectedToClose && (
                <CloseFollowModal
                    isOpen
                    follow={selectedToClose}
                    onClose={() => setSelectedToClose(null)}
                    onSuccess={refetch}
                />
            )}
        </div>
    );
}