import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import logo from '@/images/logo_with.png';
import { verifyOrganizationReport, ReportVerificationResult } from '../services/organizationReportVerificationService';
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

export function ReportVerificationPage() {
    const [searchParams] = useSearchParams();
    const [result, setResult] = useState<ReportVerificationResult | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const ref = searchParams.get('ref') ?? '';
    const organizationId = searchParams.get('organizationId') ?? '';
    const from = searchParams.get('from') ?? '';
    const to = searchParams.get('to') ?? '';

    useEffect(() => {
        const verify = async () => {
            if (!ref || !organizationId || !from || !to) {
                setError('Lien de vérification incomplet.');
                setIsLoading(false);
                return;
            }

            try {
                const verification = await verifyOrganizationReport({
                    ref,
                    organizationId,
                    from,
                    to,
                });
                setResult(verification);
            } catch {
                setError('Impossible de vérifier ce rapport pour le moment.');
            } finally {
                setIsLoading(false);
            }
        };

        verify();
    }, [ref, organizationId, from, to]);

    return (
        <div className="report-verification-page">
            <div className="report-verification-page__card">
                <div className="report-verification-page__brand">
                    <img src={logo} alt="DiabCare" className="report-verification-page__logo" />
                    <div>
                        <h1>Vérification de rapport</h1>
                        <p>Plateforme DiabCare</p>
                    </div>
                </div>

                {isLoading && <p className="report-verification-page__status">Vérification en cours...</p>}

                {!isLoading && error && (
                    <div className="report-verification-page__result report-verification-page__result--error">
                        <h2>Rapport non vérifiable</h2>
                        <p>{error}</p>
                    </div>
                )}

                {!isLoading && result && (
                    <div className={`report-verification-page__result report-verification-page__result--${result.authentic ? 'success' : 'error'}`}>
                        <h2>{result.authentic ? 'Rapport authentique' : 'Rapport non authentique'}</h2>
                        <p>{result.message}</p>

                        {result.authentic && (
                            <dl className="report-verification-page__details">
                                <div>
                                    <dt>Organisation</dt>
                                    <dd>{result.organizationName}</dd>
                                </div>
                                <div>
                                    <dt>Type de document</dt>
                                    <dd>{result.documentType}</dd>
                                </div>
                                <div>
                                    <dt>Période couverte</dt>
                                    <dd>{formatFrenchDate(result.periodFrom)} au {formatFrenchDate(result.periodTo)}</dd>
                                </div>
                                <div>
                                    <dt>Référence</dt>
                                    <dd>{result.reference}</dd>
                                </div>
                                <div>
                                    <dt>Vérifié le</dt>
                                    <dd>{formatFrenchDate(result.verifiedAt)}</dd>
                                </div>
                            </dl>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
