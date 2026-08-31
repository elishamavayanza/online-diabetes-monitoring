import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime } from '../../utils/dossierUtils';
import { getAllergySeverityLabel, getConsentTypeLabel, getDiagnosisStatusLabel } from '../../utils/labelUtils';

export function OverviewTab() {
    const { data } = usePatientDossierContext();
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
                    <h3>Informations personnelles</h3>
                    <p><strong>Nom :</strong> {profile.fullName}</p>
                    <p><strong>Date de naissance :</strong> {profile.dateOfBirth ? formatDisplayDate(profile.dateOfBirth) : '—'}</p>
                    <p><strong>Email :</strong> {profile.email || '—'}</p>
                    <p><strong>Téléphone :</strong> {profile.phone || '—'}</p>
                    <p><strong>Groupe sanguin :</strong> {profile.bloodType || '—'}</p>
                    <p><strong>Taille :</strong> {profile.heightCm ? `${profile.heightCm} cm` : '—'}</p>
                    <p><strong>Organisation :</strong> {profile.organizationName || '—'}</p>
                </Card>

                <Card>
                    <h3>Dossier médical</h3>
                    {record ? (
                        <>
                            <p>
                                <strong>Statut :</strong>{' '}
                                <Badge variant={record.status === 'open' ? 'success' : 'warning'}>
                                    {record.status === 'open' ? 'Ouvert' : 'Fermé'}
                                </Badge>
                            </p>
                            <p><strong>Ouvert le :</strong> {record.openedAt ? formatDisplayDateTime(record.openedAt) : '—'}</p>
                            {record.closedAt && (
                                <p><strong>Fermé le :</strong> {formatDisplayDateTime(record.closedAt)}</p>
                            )}
                        </>
                    ) : (
                        <p>Aucun dossier médical.</p>
                    )}
                </Card>

                <Card>
                    <h3>Résumé clinique</h3>
                    <p><strong>Allergies :</strong> {allergies.length}</p>
                    <p><strong>Diagnostics :</strong> {diagnoses.length}</p>
                    <p><strong>Consentements actifs :</strong> {activeConsents} / {consents.length}</p>
                    <p><strong>Contacts d'urgence :</strong> {emergencyContacts.length}</p>
                    <p><strong>Prescriptions :</strong> {prescriptions.length}</p>
                    <p><strong>Mesures :</strong> {totalMeasurements}</p>
                    <p><strong>Repas :</strong> {meals.length}</p>
                    <p><strong>Rendez-vous :</strong> {appointments.length}</p>
                    <p><strong>Notes :</strong> {notes.length}</p>
                </Card>

                <Card>
                    <h3>Allergies récentes</h3>
                    {allergies.length === 0 ? (
                        <p>Aucune allergie enregistrée.</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {allergies.slice(0, 3).map((allergy) => (
                                <li key={allergy.id}>
                                    <strong>{allergy.name}</strong>
                                    {allergy.severity && ` — ${getAllergySeverityLabel(allergy.severity)}`}
                                </li>
                            ))}
                            {allergies.length > 3 && <li><em>+{allergies.length - 3} autres…</em></li>}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>Diagnostics</h3>
                    {diagnoses.length === 0 ? (
                        <p>Aucun diagnostic enregistré.</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {diagnoses.slice(0, 3).map((diag) => (
                                <li key={diag.id}>
                                    <strong>{diag.conditionName}</strong>
                                    {diag.status && (
                                        <> — <Badge variant="info">{getDiagnosisStatusLabel(diag.status)}</Badge></>
                                    )}
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>Consentements</h3>
                    {consents.length === 0 ? (
                        <p>Aucun consentement enregistré.</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {consents.map((consent) => (
                                <li key={consent.id}>
                                    {getConsentTypeLabel(consent.consentType)}{' '}
                                    <Badge variant={consent.revokedAt ? 'error' : 'success'}>
                                        {consent.revokedAt ? 'Révoqué' : 'Actif'}
                                    </Badge>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>Contacts d'urgence</h3>
                    {emergencyContacts.length === 0 ? (
                        <p>Aucun contact d'urgence.</p>
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
