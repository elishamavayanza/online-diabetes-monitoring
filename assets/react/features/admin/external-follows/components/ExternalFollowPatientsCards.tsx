import React from 'react';
import { Link } from 'react-router-dom';
import '@/styles/pages/admin/external-follows/_external-follows.scss';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Avatar } from '@/react/components/UI/Avatar';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useMyExternalFollows } from '../hooks/useMyExternalFollows';
import { CloseFollowModal } from './CloseFollowModal';
import { ExternalFollowInvitation, EXTERNAL_FOLLOW_STATUS_LABELS } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

interface ExternalFollowPatientsCardsProps {
    rolePrefix: 'clinician' | 'nutritionist';
    search?: string;
    showEmptyState?: boolean;
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
}

function ExternalFollowCard({ follow, rolePrefix, onCloseClick }: {
    follow: ExternalFollowInvitation;
    rolePrefix: ExternalFollowPatientsCardsProps['rolePrefix'];
    onCloseClick: (follow: ExternalFollowInvitation) => void;
}) {
    const hasAccess = follow.status === 'ACCEPTED' || follow.status === 'PENDING';
    const { t } = useI18n();

    return (
        <Card className="clinician-patient-card external-follow-patient-card" interactive={hasAccess}>
            <div className="clinician-patient-card__photo">
                <Avatar
                    src={follow.patientPhotoUrl ?? ''}
                    name={follow.patientName}
                    size="xlarge"
                    shape="circle"
                />
            </div>

            <div className="clinician-patient-card__info">
                <h3 className="clinician-patient-card__name">{follow.patientName}</h3>
                <p className="clinician-patient-card__detail">
                    <span className="clinician-patient-card__label">{t('Organisation :')}</span>{' '}
                    {follow.organizationName}
                </p>
                <p className="clinician-patient-card__detail">
                    <span className="clinician-patient-card__label">{t("Accès jusqu\u2019au :")}</span>{' '}
                    {formatDate(follow.endDate)}
                </p>
            </div>

            <div className="clinician-patient-card__status">
                <Badge variant={follow.status === 'ACCEPTED' ? 'success' : 'warning'}>
                    {EXTERNAL_FOLLOW_STATUS_LABELS[follow.status] ?? follow.status}
                </Badge>
            </div>

            {hasAccess && (
                <div className="clinician-patient-card__action">
                    <Link to={`/${rolePrefix}/patients/${follow.patientId}/record`}>
                        <Button variant="primary" size="small">{t('Voir le dossier')}</Button>
                    </Link>
                    {follow.status === 'ACCEPTED' && (
                        <Button variant="danger" size="small" onClick={() => onCloseClick(follow)}>
                            {t('Fermer mon suivi')}
                        </Button>
                    )}
                </div>
            )}
        </Card>
    );
}

export function ExternalFollowPatientsCards({ rolePrefix, search = '', showEmptyState = true }: ExternalFollowPatientsCardsProps) {
    const { follows, isLoading, error, refetch } = useMyExternalFollows();
    const [selectedToClose, setSelectedToClose] = React.useState<ExternalFollowInvitation | null>(null);
    const { t } = useI18n();

    const filtered = search.trim()
        ? follows.filter((follow) => follow.patientName.toLowerCase().includes(search.trim().toLowerCase()))
        : follows;

    if (isLoading) {
        return (
            <div className="external-follow-patients__empty">
                <Spinner size="medium" />
            </div>
        );
    }

    if (error) {
        return (
            <div className="external-follow-patients__empty">
                <Alert variant="error">{error}</Alert>
            </div>
        );
    }

    if (filtered.length === 0) {
        if (!showEmptyState) return null;
        return (
            <div className="external-follow-patients__empty">
                <p>{t('Aucun patient suivi en externe pour le moment.')}</p>
                <p className="external-follow-patients__empty-hint">
                    {t("Lorsqu\u2019une organisation vous invitera à suivre un de ses patients, il apparaîtra ici.")}
                </p>
            </div>
        );
    }

    return (
        <div className="external-follow-patients">
            <p className="external-follow-patients__count">
                {t('{{ count }} patient(s) suivi(s) depuis une autre organisation', { count: filtered.length })}
            </p>
            <div className="clinician-patients-cards">
                {filtered.map((follow) => (
                    <ExternalFollowCard key={follow.id} follow={follow} rolePrefix={rolePrefix} onCloseClick={setSelectedToClose} />
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