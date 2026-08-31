import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createMedicalConsent, updateMedicalConsent } from '../../services/dossierActionsService';
import { PatientDossierData, PatientMedicalConsent } from '../../types';

interface MedicalConsentFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    consent?: PatientMedicalConsent;
    onSuccess: () => void;
}

const CONSENT_TYPE_OPTIONS = [
    { value: 'DATA_PROCESSING', label: 'Traitement des données' },
    { value: 'TELEMONITORING', label: 'Télémonitoring' },
    { value: 'DATA_SHARING_WITH_ORG', label: 'Partage avec l\'organisation' },
];

export function MedicalConsentFormModal({ isOpen, onClose, data, consent, onSuccess }: MedicalConsentFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        consentType: 'DATA_PROCESSING',
        grantedAt: '',
        documentUrl: '',
    });

    const isEdit = !!consent;

    useEffect(() => {
        if (isOpen) {
            setForm({
                consentType: consent?.consentType ?? 'DATA_PROCESSING',
                grantedAt: consent?.grantedAt
                    ? new Date(consent.grantedAt).toISOString().slice(0, 16)
                    : new Date().toISOString().slice(0, 16),
                documentUrl: consent?.documentUrl ?? '',
            });
            setError(null);
        }
    }, [isOpen, consent]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                organizationId: data.profile.organizationId,
                consentType: form.consentType,
                grantedAt: new Date(form.grantedAt).toISOString(),
                documentUrl: form.documentUrl || undefined,
            };
            if (isEdit && consent) {
                await updateMedicalConsent(consent.id, {
                    ...payload,
                    revokedAt: consent.revokedAt,
                });
            } else {
                await createMedicalConsent(payload);
            }
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de l\'enregistrement.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? 'Modifier le consentement' : 'Enregistrer un consentement'}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Type de consentement" htmlFor="consentType" required>
                        <Select id="consentType" name="consentType" value={form.consentType} onChange={handleChange} options={CONSENT_TYPE_OPTIONS} />
                    </FormField>
                    <FormField label="Date d'octroi" htmlFor="grantedAt" required>
                        <Input id="grantedAt" name="grantedAt" type="datetime-local" value={form.grantedAt} onChange={handleChange} required />
                    </FormField>
                    <FormField label="URL du document" htmlFor="documentUrl">
                        <Input id="documentUrl" name="documentUrl" type="url" value={form.documentUrl} onChange={handleChange} placeholder="https://..." />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : isEdit ? 'Enregistrer' : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
