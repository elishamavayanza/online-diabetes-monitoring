import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime } from '../../utils/dossierUtils';
import {
    getAllergySeverityLabel,
    getConsentTypeLabel,
    getDiagnosisStatusLabel,
} from '../../utils/labelUtils';
import {
    deleteAllergy,
    deleteDiagnosis,
    deleteEmergencyContact,
    deleteMedicalConsent,
    updateMedicalConsent,
} from '../../services/dossierActionsService';
import { PatientAllergy, PatientDiagnosis, PatientEmergencyContact, PatientMedicalConsent } from '../../types';

export function MedicalProfileTab() {
    const {
        data,
        reload,
        isReadOnly,
        openAllergyModal,
        openDiagnosisModal,
        openConsentModal,
        openEmergencyContactModal,
    } = usePatientDossierContext();

    const { allergies, diagnoses, consents, emergencyContacts, profile } = data;

    const [deleteTarget, setDeleteTarget] = useState<{
        type: 'allergy' | 'diagnosis' | 'consent' | 'contact';
        id: string;
        label: string;
    } | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = async () => {
        if (!deleteTarget) return;
        setIsDeleting(true);
        try {
            switch (deleteTarget.type) {
                case 'allergy':
                    await deleteAllergy(deleteTarget.id);
                    break;
                case 'diagnosis':
                    await deleteDiagnosis(deleteTarget.id);
                    break;
                case 'consent':
                    await deleteMedicalConsent(deleteTarget.id);
                    break;
                case 'contact':
                    await deleteEmergencyContact(deleteTarget.id);
                    break;
            }
            reload();
            setDeleteTarget(null);
        } finally {
            setIsDeleting(false);
        }
    };

    const handleRevokeConsent = async (consent: PatientMedicalConsent) => {
        await updateMedicalConsent(consent.id, {
            patientId: profile.id,
            organizationId: consent.organizationId,
            consentType: consent.consentType ?? 'DATA_PROCESSING',
            grantedAt: consent.grantedAt,
            revokedAt: new Date().toISOString(),
            documentUrl: consent.documentUrl,
        });
        reload();
    };

    const renderActions = (
        type: 'allergy' | 'diagnosis' | 'consent' | 'contact',
        id: string,
        label: string,
        onEdit: () => void,
        extraActions?: React.ReactNode,
    ) => {
        if (isReadOnly) return null;
        return (
            <div className="patient-dossier-tab__item-actions">
                {extraActions}
                <Button variant="secondary" size="small" onClick={onEdit}>Modifier</Button>
                <Button variant="danger" size="small" onClick={() => setDeleteTarget({ type, id, label })}>
                    Supprimer
                </Button>
            </div>
        );
    };

    return (
        <div className="patient-dossier-tab patient-dossier-tab--medical-profile">
            <div className="patient-dossier-tab__section">
                <div className="patient-dossier-tab__toolbar">
                    <h3>Allergies</h3>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openAllergyModal()}>
                            + Ajouter une allergie
                        </Button>
                    )}
                </div>
                {allergies.length === 0 ? (
                    <Card><p>Aucune allergie enregistrée.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__grid">
                        {allergies.map((allergy: PatientAllergy) => (
                            <Card key={allergy.id}>
                                <div className="patient-dossier-tab__card-header">
                                    <h4>{allergy.name}</h4>
                                    {allergy.severity && (
                                        <Badge variant={allergy.severity === 'SEVERE' ? 'error' : 'warning'}>
                                            {getAllergySeverityLabel(allergy.severity)}
                                        </Badge>
                                    )}
                                </div>
                                {allergy.reaction && <p><strong>Réaction :</strong> {allergy.reaction}</p>}
                                {allergy.notes && <p><strong>Notes :</strong> {allergy.notes}</p>}
                                {renderActions('allergy', allergy.id, allergy.name, () => openAllergyModal(allergy))}
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <div className="patient-dossier-tab__section">
                <div className="patient-dossier-tab__toolbar">
                    <h3>Diagnostics</h3>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openDiagnosisModal()}>
                            + Ajouter un diagnostic
                        </Button>
                    )}
                </div>
                {diagnoses.length === 0 ? (
                    <Card><p>Aucun diagnostic enregistré.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__grid">
                        {diagnoses.map((diag: PatientDiagnosis) => (
                            <Card key={diag.id}>
                                <div className="patient-dossier-tab__card-header">
                                    <h4>{diag.conditionName}</h4>
                                    {diag.status && <Badge variant="info">{getDiagnosisStatusLabel(diag.status)}</Badge>}
                                </div>
                                {diag.diagnosedAt && <p><strong>Date :</strong> {formatDisplayDate(diag.diagnosedAt)}</p>}
                                {diag.description && <p><strong>Description :</strong> {diag.description}</p>}
                                {renderActions('diagnosis', diag.id, diag.conditionName, () => openDiagnosisModal(diag))}
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <div className="patient-dossier-tab__section">
                <div className="patient-dossier-tab__toolbar">
                    <h3>Consentements médicaux</h3>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openConsentModal()}>
                            + Enregistrer un consentement
                        </Button>
                    )}
                </div>
                {consents.length === 0 ? (
                    <Card><p>Aucun consentement enregistré.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__grid">
                        {consents.map((consent: PatientMedicalConsent) => (
                            <Card key={consent.id}>
                                <div className="patient-dossier-tab__card-header">
                                    <h4>{getConsentTypeLabel(consent.consentType)}</h4>
                                    <Badge variant={consent.revokedAt ? 'error' : 'success'}>
                                        {consent.revokedAt ? 'Révoqué' : 'Actif'}
                                    </Badge>
                                </div>
                                <p><strong>Accordé le :</strong> {formatDisplayDateTime(consent.grantedAt)}</p>
                                {consent.revokedAt && (
                                    <p><strong>Révoqué le :</strong> {formatDisplayDateTime(consent.revokedAt)}</p>
                                )}
                                {consent.documentUrl && (
                                    <p>
                                        <strong>Document :</strong>{' '}
                                        <a href={consent.documentUrl} target="_blank" rel="noopener noreferrer">
                                            Voir le document
                                        </a>
                                    </p>
                                )}
                                {renderActions(
                                    'consent',
                                    consent.id,
                                    getConsentTypeLabel(consent.consentType),
                                    () => openConsentModal(consent),
                                    !isReadOnly && !consent.revokedAt ? (
                                        <Button variant="outline" size="small" onClick={() => handleRevokeConsent(consent)}>
                                            Révoquer
                                        </Button>
                                    ) : null,
                                )}
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <div className="patient-dossier-tab__section">
                <div className="patient-dossier-tab__toolbar">
                    <h3>Contacts d'urgence</h3>
                    {!isReadOnly && (
                        <Button variant="primary" size="small" onClick={() => openEmergencyContactModal()}>
                            + Ajouter un contact
                        </Button>
                    )}
                </div>
                {emergencyContacts.length === 0 ? (
                    <Card><p>Aucun contact d'urgence.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__grid">
                        {emergencyContacts.map((contact: PatientEmergencyContact) => (
                            <Card key={contact.id}>
                                <h4>{contact.fullName}</h4>
                                {contact.relationship && <p><strong>Relation :</strong> {contact.relationship}</p>}
                                {contact.phone && <p><strong>Téléphone :</strong> {contact.phone}</p>}
                                {renderActions('contact', contact.id, contact.fullName, () => openEmergencyContactModal(contact))}
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <ConfirmDialog
                isOpen={!!deleteTarget}
                onClose={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Confirmer la suppression"
                message={`Voulez-vous vraiment supprimer « ${deleteTarget?.label} » ?`}
                confirmLabel={isDeleting ? 'Suppression...' : 'Supprimer'}
                cancelLabel="Annuler"
            />
        </div>
    );
}
