import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { useI18n } from '@/react/i18n/I18nContext';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime } from '../../utils/dossierUtils';
import { getAllergySeverityLabel, getConsentTypeLabel, getDiagnosisStatusLabel } from '../../utils/labelUtils';

export function OverviewTab() {
    const { data } = usePatientDossierContext();
    const { t } = useI18n();
    const {
        profile,
        allergies,
        diagnoses,
        emergencyContacts,
        consents,
        record,
        prescriptions,
        meals,
        measurements,
        appointments,
        notes,
    } = data;

    const activeConsents = consents.filter((c) => !c.revokedAt).length;
    const totalMeasurements =
        measurements.bloodGlucose.length +
        measurements.bloodPressure.length +
        measurements.hba1c.length +
        measurements.weight.length +
        measurements.physicalActivity.length +
        measurements.laboratoryResults.length;

    return (
        <div className="patient-dossier-tab patient-dossier-tab--overview">
            <div className="patient-dossier-tab__grid">
                <Card>
                    <h3>{t('Informations personnelles')}</h3>
                    <p><strong>{t('Nom :')}</strong> {profile.fullName}</p>
                    <p><strong>{t('Date de naissance :')}</strong> {profile.dateOfBirth ? formatDisplayDate(profile.dateOfBirth) : '—'}</p>
                    <p><strong>{t('Email :')}</strong> {profile.email || '—'}</p>
                    <p><strong>{t('Téléphone :')}</strong> {profile.phone || '—'}</p>
                    <p><strong>{t('Groupe sanguin :')}</strong> {profile.bloodType || '—'}</p>
                    <p><strong>{t('Taille :')}</strong> {profile.heightCm ? `${profile.heightCm} cm` : '—'}</p>
                    <p><strong>{t('Organisation :')}</strong> {profile.organizationName || '—'}</p>
                </Card>

                <Card>
                    <h3>{t('Dossier médical')}</h3>
                    {record ? (
                        <>
                            <p>
                                <strong>{t('Statut :')}</strong>{' '}
                                <Badge variant={record.status === 'open' ? 'success' : 'warning'}>
                                    {record.status === 'open' ? t('Ouvert') : t('Fermé')}
                                </Badge>
                            </p>
                            <p><strong>{t('Ouvert le :')}</strong> {record.openedAt ? formatDisplayDateTime(record.openedAt) : '—'}</p>
                            {record.closedAt && (
                                <p><strong>{t('Fermé le :')}</strong> {formatDisplayDateTime(record.closedAt)}</p>
                            )}
                        </>
                    ) : (
                        <p>{t('Aucun dossier médical.')}</p>
                    )}
                </Card>

                <Card>
                    <h3>{t('Résumé clinique')}</h3>
                    <p><strong>{t('Allergies :')}</strong> {allergies.length}</p>
                    <p><strong>{t('Diagnostics :')}</strong> {diagnoses.length}</p>
                    <p><strong>{t('Consentements actifs :')}</strong> {activeConsents} / {consents.length}</p>
                    <p><strong>{t("Contacts d'urgence :")}</strong> {emergencyContacts.length}</p>
                    <p><strong>{t('Prescriptions :')}</strong> {prescriptions.length}</p>
                    <p><strong>{t('Mesures :')}</strong> {totalMeasurements}</p>
                    <p><strong>{t('Repas :')}</strong> {meals.length}</p>
                    <p><strong>{t('Rendez-vous :')}</strong> {appointments.length}</p>
                    <p><strong>{t('Notes :')}</strong> {notes.length}</p>
                </Card>

                <Card>
                    <h3>{t('Allergies récentes')}</h3>
                    {allergies.length === 0 ? (
                        <p>{t('Aucune allergie enregistrée.')}</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {allergies.slice(0, 3).map((allergy) => (
                                <li key={allergy.id}>
                                    <strong>{allergy.name}</strong>
                                    {allergy.severity && <> — {t(getAllergySeverityLabel(allergy.severity))}</>}
                                </li>
                            ))}
                            {allergies.length > 3 && <li><em>{t('+{{ count }} autres…', { count: allergies.length - 3 })}</em></li>}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>{t('Diagnostics')}</h3>
                    {diagnoses.length === 0 ? (
                        <p>{t('Aucun diagnostic enregistré.')}</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {diagnoses.slice(0, 3).map((diag) => (
                                <li key={diag.id}>
                                    <strong>{diag.conditionName}</strong>
                                    {diag.status && (
                                        <>{' '}<Badge variant="info">{t(getDiagnosisStatusLabel(diag.status))}</Badge></>
                                    )}
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>{t('Consentements')}</h3>
                    {consents.length === 0 ? (
                        <p>{t('Aucun consentement enregistré.')}</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {consents.map((consent) => (
                                <li key={consent.id}>
                                    {t(getConsentTypeLabel(consent.consentType))}{' '}
                                    <Badge variant={consent.revokedAt ? 'error' : 'success'}>
                                        {consent.revokedAt ? t('Révoqué') : t('Actif')}
                                    </Badge>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>{t("Contacts d'urgence")}</h3>
                    {emergencyContacts.length === 0 ? (
                        <p>{t("Aucun contact d'urgence.")}</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {emergencyContacts.map((contact) => (
                                <li key={contact.id}>
                                    <strong>{contact.fullName}</strong>
                                    {contact.relationship && ` (${contact.relationship})`}
                                    {contact.phone && ` — ${contact.phone}`}
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>
            </div>
        </div>
    );
}
