import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { fetchInvitationByToken, acceptInvitation, declineInvitation } from '../services/externalFollowsService';
import { ExternalFollowInvitation, EXTERNAL_FOLLOW_STATUS_LABELS } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
}

export function AcceptInvitationPage() {
    const { token } = useParams<{ token: string }>();
    const { isAuthenticated, user } = useAuth();
    const { t } = useI18n();
    const [invitation, setInvitation] = useState<ExternalFollowInvitation | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [actionError, setActionError] = useState<string | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [success, setSuccess] = useState(false);
    const [isDeclined, setIsDeclined] = useState(false);

    useEffect(() => {
        if (!token) return;
        let cancelled = false;
        fetchInvitationByToken(token)
            .then((data) => {
                if (!cancelled) setInvitation(data);
            })
            .catch((err) => {
                if (!cancelled) setError(err instanceof Error ? err.message : t('Invitation introuvable.'));
            })
            .finally(() => {
                if (!cancelled) setIsLoading(false);
            });
        return () => {
            cancelled = true;
        };
    }, [token]);

    if (!token) {
        return (
            <div className="invite-public-page">
                <Alert variant="error">{t("Lien d'invitation invalide.")}</Alert>
            </div>
        );
    }

    if (isLoading) {
        return (
            <div className="invite-public-page">
                <Spinner size="medium" />
            </div>
        );
    }

    if (error || !invitation) {
        return (
            <div className="invite-public-page">
                <Alert variant="error">{error ?? t('Invitation introuvable.')}</Alert>
                <Link to="/login">
                    <Button variant="outline" className="invite-public-page__cta">Retour à la connexion</Button>
                </Link>
            </div>
        );
    }

    if (invitation.status !== 'PENDING') {
        const message =
            invitation.status === 'ACCEPTED'
                ? t('Cette invitation a déjà été acceptée.')
                : invitation.status === 'DECLINED'
                    ? t('Cette invitation a été refusée.')
                    : invitation.status === 'REVOKED'
                        ? t('Cette invitation a été révoquée.')
                        : t('Cette invitation n’est plus active.');
        return (
            <div className="invite-public-page invite-public-page--final">
                <h1>{EXTERNAL_FOLLOW_STATUS_LABELS[invitation.status]}</h1>
                <p>{message}</p>
                <Link to="/login">
                    <Button variant="outline" className="invite-public-page__cta">{t('Retour à la connexion')}</Button>
                </Link>
            </div>
        );
    }

    if (!isAuthenticated) {
        return (
            <div className="invite-public-page invite-public-page--login">
                <div className="invite-public-page__card">
                    <h1>{t('Invitation de suivi externe')}</h1>
                    <p className="invite-public-page__intro">
                        {t('{{ invitedBy }} ({{ organization }}) vous invite à suivre le patient {{ patient }} jusqu\'au {{ endDate }}.', { invitedBy: invitation.invitedByName, organization: invitation.organizationName, patient: invitation.patientName, endDate: formatDate(invitation.endDate) })}
                    </p>
                    {invitation.message && (
                        <p className="invite-public-page__message">
                            « {invitation.message} »
                        </p>
                    )}
                    <Alert variant="warning">
                        {t('Vous devez être connecté avec le compte {{ email }} pour accepter.', { email: invitation.email })}
                    </Alert>
                    <div className="invite-public-page__actions">
                        <Link to="/login">
                            <Button
                                onClick={() => {
                                    if (token) sessionStorage.setItem('pendingInviteToken', token);
                                }}
                            >
                                {t('Se connecter pour accepter')}
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        );
    }

    const emailMatch =
        user?.email?.toLowerCase() === invitation.email.toLowerCase();

    if (!emailMatch) {
        return (
            <div className="invite-public-page invite-public-page--wrong-account">
                <Alert variant="error">
                    {t('Cette invitation est addressée à {{ email }}. Vous êtes connecté en tant que {{ currentEmail }}.', { email: invitation.email, currentEmail: user?.email ?? '' })}
                </Alert>
                <Link to="/login">
                    <Button variant="outline">{t('Se connecter avec le bon compte')}</Button>
                </Link>
            </div>
        );
    }

    if (success || isDeclined) {
        return (
            <div className="invite-public-page invite-public-page--final">
                {success && (
                    <>
                        <h1>{t('Invitation acceptée')}</h1>
                        <p>{t('Vous pouvez désormais accéder au dossier de {{ patient }}.', { patient: invitation.patientName })}</p>
                        <Link to={`/clinician/patients/${invitation.patientId}/record`}>
                            <Button className="invite-public-page__cta">{t('Accéder au dossier')}</Button>
                        </Link>
                    </>
                )}
                {isDeclined && (
                    <>
                        <h1>{t('Invitation refusée')}</h1>
                        <p>{t('Vous avez refusé l\'invitation au suivi de {{ patient }}.', { patient: invitation.patientName })}</p>
                        <Link to="/login">
                            <Button variant="outline" className="invite-public-page__cta">{t('Retour à l\'accueil')}</Button>
                        </Link>
                    </>
                )}
            </div>
        );
    }

    const handleAccept = async () => {
        setActionError(null);
        setIsSubmitting(true);
        try {
            await acceptInvitation(token);
            setSuccess(true);
        } catch (err) {
            setActionError(err instanceof Error ? err.message : 'Erreur lors de l' + "'" + "acceptation.");
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleDecline = async () => {
        setActionError(null);
        setIsSubmitting(true);
        try {
            await declineInvitation(token);
            setIsDeclined(true);
        } catch (err) {
            setActionError(err instanceof Error ? err.message : 'Erreur lors du refus.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="invite-public-page invite-public-page--pending">
            <div className="invite-public-page__card">
                <h1>Invitation de suivi externe</h1>
                <p className="invite-public-page__intro">
                    <strong>{invitation.invitedByName}</strong> ({invitation.organizationName}) vous invite à suivre le patient <strong>{invitation.patientName}</strong> jusqu'au <strong>{formatDate(invitation.endDate)}</strong>.
                </p>
                {invitation.message && (
                    <p className="invite-public-page__message">
                        « {invitation.message} »
                    </p>
                )}
                {actionError && <Alert variant="error">{actionError}</Alert>}
                <div className="invite-public-page__actions">
                    <Button onClick={handleAccept} disabled={isSubmitting}>
                        {isSubmitting ? 'Traitement...' : 'Accepter l' + "'" + 'invitation'}
                    </Button>
                    <Button variant="outline" onClick={handleDecline} disabled={isSubmitting}>
                        Refuser
                    </Button>
                </div>
            </div>
        </div>
    );
}