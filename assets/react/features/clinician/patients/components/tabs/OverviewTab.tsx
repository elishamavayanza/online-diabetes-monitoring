import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime } from '../../utils/dossierUtils';

export function OverviewTab() {
    const { data } = usePatientDossierContext();
    const { profile, allergies, diagnoses, emergencyContacts, record } = data;

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
                    <h3>Allergies</h3>
                    {allergies.length === 0 ? (
                        <p>Aucune allergie enregistrée.</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {allergies.map((allergy) => (
                                <li key={allergy.id}>
                                    <strong>{allergy.name}</strong>
                                    {allergy.severity && ` — ${allergy.severity}`}
                                    {allergy.reaction && ` (${allergy.reaction})`}
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>

                <Card>
                    <h3>Diagnostics</h3>
                    {diagnoses.length === 0 ? (
                        <p>Aucun diagnostic enregistré.</p>
                    ) : (
                        <ul className="patient-dossier-tab__list">
                            {diagnoses.map((diag) => (
                                <li key={diag.id}>
                                    <strong>{diag.conditionName}</strong>
                                    {diag.diagnosedAt && ` — ${formatDisplayDate(diag.diagnosedAt)}`}
                                    {diag.status && (
                                        <Badge variant="info">{diag.status}</Badge>
                                    )}
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
