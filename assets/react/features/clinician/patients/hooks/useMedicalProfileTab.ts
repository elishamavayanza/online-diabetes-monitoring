import { useState } from 'react';
import { usePatientDossierContext } from '../contexts/PatientDossierContext';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { useAuth } from '@/react/app/providers/AuthProvider';
import {
    deleteAllergy,
    deleteDiagnosis,
    deleteEmergencyContact,
    deleteMedicalConsent, downloadMedicalConsentFile,
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
    const { user } = useAuth();
    const isClinician = user?.role === 'CLINICIAN' || user?.role === 'NUTRITIONIST';

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
    const handleDownloadConsentDocument = async (consent: PatientMedicalConsent) => {
        try {
            await downloadMedicalConsentFile(consent.id);
            showToast({ type: 'success', message: 'Téléchargement démarré.' });
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors du téléchargement.';
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
        handleDownloadConsentDocument,
    };
}
