import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import logo from '@/images/logo_with.png';
import { verifyOrganizationReport, ReportVerificationResult } from '../services/organizationReportVerificationService';
import {
    verifyPatientFollowUpReport,
    PatientReportVerificationResult,
} from '@/react/features/clinician/patients/services/patientFollowUpReportVerificationService';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/reports/_verification.scss';

function formatFrenchDate(value: string): string {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date);
}

type VerificationState =
    | { kind: 'organization'; result: ReportVerificationResult }
    | { kind: 'patient'; result: PatientReportVerificationResult };

export function ReportVerificationPage() {
    const [searchParams] = useSearchParams();
    const [verification, setVerification] = useState<VerificationState | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { t } = useI18n();

    const ref = searchParams.get('ref') ?? '';
    const type = searchParams.get('type') ?? (ref.startsWith('RPT-PAT-') ? 'patient' : 'organization');
    const organizationId = searchParams.get('organizationId') ?? '';
    const patientId = searchParams.get('patientId') ?? '';
    const from = searchParams.get('from') ?? '';
    const to = searchParams.get('to') ?? '';

    useEffect(() => {
        const verify = async () => {
            if (!ref || !from || !to) {
                setError(t('Lien de vérification incomplet.'));
                setIsLoading(false);
                return;
            }

            try {
                if (type === 'patient') {
                    if (!patientId) {
                        setError(t('Lien de vérification incomplet.'));
                        setIsLoading(false);
                        return;
                    }

                    const result = await verifyPatientFollowUpReport({
                        ref,
                        patientId,
                        from,
                        to,
                    });
                    setVerification({ kind: 'patient', result });
                } else {
                    if (!organizationId) {
                        setError(t('Lien de vérification incomplet.'));
                        setIsLoading(false);
                        return;
                    }

                    const result = await verifyOrganizationReport({
                        ref,
                        organizationId,
                        from,
                        to,
                    });
                    setVerification({ kind: 'organization', result });
                }
            } catch {
                setError(t('Impossible de vérifier ce rapport pour le moment.'));
            } finally {
                setIsLoading(false);
            }
        };

        verify();
    }, [ref, type, organizationId, patientId, from, to]);

    const result = verification?.result ?? null;

    return (
        <div className="report-verification-page">
            <div className="report-verification-page__card">
                <div className="report-verification-page__brand">
                    <img src={logo} alt="DiabCare" className="report-verification-page__logo" />
                    <div>
                        <h1>{t('Vérification de rapport')}</h1>
                        <p>{t('Plateforme OnlineDIAB')}</p>
                    </div>
                </div>

                {isLoading && <p className="report-verification-page__status">{t('Vérification en cours...')}</p>}

                {!isLoading && error && (
                    <div className="report-verification-page__result report-verification-page__result--error">
                        <h2>{t('Rapport non vérifiable')}</h2>
                        <p>{error}</p>
                    </div>
                )}

                {!isLoading && result && (
                    <div className={`report-verification-page__result report-verification-page__result--${result.authentic ? 'success' : 'error'}`}>
                        <h2>{result.authentic ? t('Rapport authentique') : t('Rapport non authentique')}</h2>
                        <p>{result.message}</p>

                        {result.authentic && verification?.kind === 'organization' && (
                            <dl className="report-verification-page__details">
                                <div>
                                    <dt>{t('Organisation')}</dt>
                                    <dd>{verification.result.organizationName}</dd>
                                </div>
                                <div>
                                    <dt>{t('Type de document')}</dt>
                                    <dd>{verification.result.documentType}</dd>
                                </div>
                                <div>
                                    <dt>{t('Période couverte')}</dt>
                                    <dd>{formatFrenchDate(verification.result.periodFrom)} {t('au')} {formatFrenchDate(verification.result.periodTo)}</dd>
                                </div>
                                <div>
                                    <dt>{t('Référence')}</dt>
                                    <dd>{verification.result.reference}</dd>
                                </div>
                                <div>
                                    <dt>{t('Vérifié le')}</dt>
                                    <dd>{formatFrenchDate(verification.result.verifiedAt)}</dd>
                                </div>
                            </dl>
                        )}

                        {result.authentic && verification?.kind === 'patient' && (
                            <dl className="report-verification-page__details">
                                <div>
                                    <dt>{t('Patient')}</dt>
                                    <dd>{verification.result.patientFullName}</dd>
                                </div>
                                {verification.result.organizationName && (
                                    <div>
                                        <dt>{t('Organisation')}</dt>
                                        <dd>{verification.result.organizationName}</dd>
                                    </div>
                                )}
                                <div>
                                    <dt>{t('Type de document')}</dt>
                                    <dd>{verification.result.documentType}</dd>
                                </div>
                                <div>
                                    <dt>{t('Période couverte')}</dt>
                                    <dd>{formatFrenchDate(verification.result.periodFrom)} {t('au')} {formatFrenchDate(verification.result.periodTo)}</dd>
                                </div>
                                <div>
                                    <dt>{t('Référence')}</dt>
                                    <dd>{verification.result.reference}</dd>
                                </div>
                                <div>
                                    <dt>{t('Vérifié le')}</dt>
                                    <dd>{formatFrenchDate(verification.result.verifiedAt)}</dd>
                                </div>
                            </dl>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
