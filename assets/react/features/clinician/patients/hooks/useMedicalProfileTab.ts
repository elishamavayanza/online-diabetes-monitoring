import { useState } from 'react';
import { usePatientDossierContext } from '../contexts/PatientDossierContext';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {
    deleteAllergy,
    deleteDiagnosis,
    deleteEmergencyContact,
    deleteMedicalConsent,
    updateMedicalConsent,
} from '../services/dossierActionsService';
import { PatientMedicalConsent } from '../types';

export type DeleteTarget = {
    type: 'allergy' | 'diagnosis' | 'consent' | 'contact';
    id: string;
    label: string;
};

export function useMedicalProfileTab() {
    const {
        data,
        reload,
        isReadOnly,
        openAllergyModal,
        openDiagnosisModal,
        openConsentModal,
        openEmergencyContactModal,
    } = usePatientDossierContext();
    const { showToast } = useToast();

    const [deleteTarget, setDeleteTarget] = useState<DeleteTarget | null>(null);
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
            showToast({
                type: 'success',
                message: `${deleteTarget.label} supprimé avec succès.`,
            });
            setDeleteTarget(null);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la suppression.';
            showToast({ type: 'error', message });
        } finally {
            setIsDeleting(false);
        }
    };

    const handleRevokeConsent = async (consent: PatientMedicalConsent) => {
        try {
            await updateMedicalConsent(consent.id, {
                patientId: data.profile.id,
                organizationId: consent.organizationId,
                consentType: consent.consentType ?? 'DATA_PROCESSING',
                grantedAt: consent.grantedAt,
                revokedAt: new Date().toISOString(),
                documentUrl: consent.documentUrl,
            });
            reload();
            showToast({
                type: 'success',
                message: 'Consentement révoqué avec succès.',
            });
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la révocation.';
            showToast({ type: 'error', message });
        }
    };

    return {
        data,
        reload,
        isReadOnly,
        openAllergyModal,
        openDiagnosisModal,
        openConsentModal,
        openEmergencyContactModal,
        deleteTarget,
        setDeleteTarget,
        isDeleting,
        handleDelete,
        handleRevokeConsent,
    };
}
