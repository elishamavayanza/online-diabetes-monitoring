import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { MedicalRecordData } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

function formatDate(dateStr: string): string {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

interface MedicalRecordSectionsProps {
    data: MedicalRecordData;
}

export function MedicalRecordSections({ data }: MedicalRecordSectionsProps) {
    const { t } = useI18n();
    return (
        <div className="medical-record-sections">
            <Card>
                <h3>{t('Informations personnelles')}</h3>
                <div className="medical-record-info">
                    <p><strong>{t('Nom :')}</strong> {data.personalInfo.nom}</p>
                    <p><strong>{t('Date de naissance :')}</strong> {formatDate(data.personalInfo.dateNaissance)}</p>
                    <p><strong>{t('Email :')}</strong> {data.personalInfo.email}</p>
                    <p><strong>{t('Téléphone :')}</strong> {data.personalInfo.telephone}</p>
                    {data.personalInfo.bloodType && (
                        <p><strong>{t('Groupe sanguin :')}</strong> {data.personalInfo.bloodType}</p>
                    )}
                    {data.personalInfo.heightCm != null && (
                        <p><strong>{t('Taille :')}</strong> {data.personalInfo.heightCm} cm</p>
                    )}
                </div>
            </Card>

            <Card>
                <h3>{t('Diabète')}</h3>
                <div className="medical-record-info">
                    <p><strong>{t('Type :')}</strong> {data.diabetesInfo.type}</p>
                    <p><strong>{t('Date du diagnostic :')}</strong> {formatDate(data.diabetesInfo.dateDiagnostic)}</p>
                </div>
            </Card>

            <Card>
                <h3>{t('Allergies')}</h3>
                {data.allergies.length === 0 ? (
                    <p className="medical-record-sections__empty">{t('Aucune allergie connue.')}</p>
                ) : (
                    <ul className="allergy-list">
                        {data.allergies.map((allergy) => (
                            <li key={allergy.id} className="allergy-list__item">
                                <div className="allergy-list__heading">
                                    <strong>{allergy.name}</strong>
                                    {allergy.severity && (
                                        <Badge variant={
                                            allergy.severity.toLowerCase().includes('gra')
                                                ? 'error'
                                                : allergy.severity.toLowerCase().includes('mod')
                                                    ? 'warning'
                                                    : 'info'
                                        }>
                                            {allergy.severity}
                                        </Badge>
                                    )}
                                </div>
                                {allergy.reaction && <span className="allergy-list__reaction">{allergy.reaction}</span>}
                                {allergy.notes && <span className="allergy-list__notes">{allergy.notes}</span>}
                            </li>
                        ))}
                    </ul>
                )}
            </Card>

            <Card>
                <h3>{t('Diagnostics')}</h3>
                {data.diagnostics.length === 0 ? (
                    <p className="medical-record-sections__empty">{t('Aucun diagnostic enregistré.')}</p>
                ) : (
                    <ul className="diagnostics-list">
                        {data.diagnostics.map((diag) => (
                            <li key={diag.id} className="diagnostics-list__item">
                                <div className="diagnostics-list__heading">
                                    <strong>{diag.nom}</strong>
                                    <span>{formatDate(diag.date)}</span>
                                </div>
                                {diag.description && <p className="diagnostics-list__desc">{diag.description}</p>}
                                {diag.status && <Badge variant="info">{t(diag.status)}</Badge>}
                            </li>
                        ))}
                    </ul>
                )}
            </Card>

            <Card>
                <h3>{t('Contacts d\u2019urgence')}</h3>
                {data.emergencyContacts.length === 0 ? (
                    <p className="medical-record-sections__empty">{t('Aucun contact d\u2019urgence.')}</p>
                ) : (
                    <ul className="emergency-contacts-list">
                        {data.emergencyContacts.map((contact, idx) => (
                            <li key={idx} className="emergency-contacts-list__item">
                                <strong>{contact.nom}</strong>
                                <span>{contact.relation}</span>
                                <span>{contact.telephone}</span>
                            </li>
                        ))}
                    </ul>
                )}
            </Card>

            <Card>
                <h3>{t('Consentements')}</h3>
                {data.consentements.length === 0 ? (
                    <p className="medical-record-sections__empty">{t('Aucun consentement enregistré.')}</p>
                ) : (
                    <ul className="consents-list">
                        {data.consentements.map((consent) => (
                            <li key={consent.id} className="consents-list__item">
                                <div>
                                    <strong>{consent.type}</strong>
                                    {consent.date && <span className="consents-list__date">{formatDate(consent.date)}</span>}
                                </div>
                                <Badge variant={consent.statut === 'Accepté' ? 'success' : 'error'}>
                                    {t(consent.statut)}
                                </Badge>
                            </li>
                        ))}
                    </ul>
                )}
            </Card>
        </div>
    );
}