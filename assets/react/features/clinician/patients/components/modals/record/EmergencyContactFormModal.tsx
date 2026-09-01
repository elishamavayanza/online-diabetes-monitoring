import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createEmergencyContact, updateEmergencyContact } from '../../../services/dossierActionsService';
import { PatientDossierData, PatientEmergencyContact } from '../../../types';

interface EmergencyContactFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    contact?: PatientEmergencyContact;
    onSuccess: () => void;
}

export function EmergencyContactFormModal({ isOpen, onClose, data, contact, onSuccess }: EmergencyContactFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        fullName: '',
        relationship: '',
        phone: '',
        email: '',
    });

    const isEdit = !!contact;

    useEffect(() => {
        if (isOpen) {
            setForm({
                fullName: contact?.fullName ?? '',
                relationship: contact?.relationship ?? '',
                phone: contact?.phone ?? '',
                email: '',
            });
            setError(null);
        }
    }, [isOpen, contact]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                fullName: form.fullName,
                relationship: form.relationship,
                phone: form.phone,
                email: form.email || undefined,
            };
            if (isEdit && contact) {
                await updateEmergencyContact(contact.id, payload);
            } else {
                await createEmergencyContact(payload);
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
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? 'Modifier le contact' : 'Ajouter un contact d\'urgence'}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Nom complet" htmlFor="fullName" required>
                        <Input id="fullName" name="fullName" value={form.fullName} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Relation" htmlFor="relationship" required>
                        <Input id="relationship" name="relationship" value={form.relationship} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Téléphone" htmlFor="phone" required>
                        <Input id="phone" name="phone" value={form.phone} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Email" htmlFor="email">
                        <Input id="email" name="email" type="email" value={form.email} onChange={handleChange} />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : isEdit ? 'Enregistrer' : 'Ajouter'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
