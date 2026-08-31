import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createAllergy, updateAllergy } from '../../services/dossierActionsService';
import { PatientAllergy, PatientDossierData } from '../../types';

interface AllergyFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    allergy?: PatientAllergy;
    onSuccess: () => void;
}

const SEVERITY_OPTIONS = [
    { value: 'MILD', label: 'Légère' },
    { value: 'MODERATE', label: 'Modérée' },
    { value: 'SEVERE', label: 'Sévère' },
];

export function AllergyFormModal({ isOpen, onClose, data, allergy, onSuccess }: AllergyFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        name: '',
        severity: 'MODERATE',
        reaction: '',
        notes: '',
        diagnosedAt: '',
    });

    const isEdit = !!allergy;

    useEffect(() => {
        if (isOpen) {
            setForm({
                name: allergy?.name ?? '',
                severity: allergy?.severity ?? 'MODERATE',
                reaction: allergy?.reaction ?? '',
                notes: allergy?.notes ?? '',
                diagnosedAt: allergy
                    ? new Date().toISOString().slice(0, 16)
                    : new Date().toISOString().slice(0, 16),
            });
            setError(null);
        }
    }, [isOpen, allergy]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                name: form.name,
                severity: form.severity,
                reaction: form.reaction || undefined,
                notes: form.notes || undefined,
                diagnosedAt: new Date(form.diagnosedAt).toISOString(),
            };
            if (isEdit && allergy) {
                await updateAllergy(allergy.id, payload);
            } else {
                await createAllergy(payload);
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
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? 'Modifier l\'allergie' : 'Ajouter une allergie'}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Allergène" htmlFor="name" required>
                        <Input id="name" name="name" value={form.name} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Sévérité" htmlFor="severity" required>
                        <Select id="severity" name="severity" value={form.severity} onChange={handleChange} options={SEVERITY_OPTIONS} />
                    </FormField>
                    <FormField label="Date du diagnostic" htmlFor="diagnosedAt" required>
                        <Input id="diagnosedAt" name="diagnosedAt" type="datetime-local" value={form.diagnosedAt} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Réaction" htmlFor="reaction">
                        <Input id="reaction" name="reaction" value={form.reaction} onChange={handleChange} />
                    </FormField>
                    <FormField label="Notes" htmlFor="notes">
                        <Textarea id="notes" name="notes" rows={3} value={form.notes} onChange={handleChange} fullWidth />
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
