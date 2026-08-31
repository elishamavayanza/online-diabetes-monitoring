import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createPrescription } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';

interface PrescriptionFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    onSuccess: () => void;
}

const STATUS_OPTIONS = [
    { value: 'ACTIVE', label: 'Active' },
    { value: 'DRAFT', label: 'Brouillon' },
];

export function PrescriptionFormModal({ isOpen, onClose, data, onSuccess }: PrescriptionFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        startDate: '',
        endDate: '',
        status: 'ACTIVE',
        notes: '',
    });

    useEffect(() => {
        if (isOpen) {
            const today = new Date().toISOString().slice(0, 16);
            setForm({ startDate: today, endDate: '', status: 'ACTIVE', notes: '' });
            setError(null);
        }
    }, [isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const prescriberId = getCurrentUserIdFromToken();
        const organizationId = data.profile.organizationId;
        if (!prescriberId || !organizationId) {
            setError('Impossible d\'identifier le prescripteur ou l\'organisation.');
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            await createPrescription({
                patientId: data.profile.id,
                prescriberId,
                organizationId,
                startDate: new Date(form.startDate).toISOString(),
                endDate: form.endDate ? new Date(form.endDate).toISOString() : undefined,
                status: form.status,
                notes: form.notes || undefined,
            });
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la création.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouvelle prescription">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Patient">
                        <Input value={data.profile.fullName} disabled />
                    </FormField>
                    <FormField label="Date de début" htmlFor="startDate" required>
                        <Input id="startDate" name="startDate" type="datetime-local" value={form.startDate} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Date de fin" htmlFor="endDate">
                        <Input id="endDate" name="endDate" type="datetime-local" value={form.endDate} onChange={handleChange} />
                    </FormField>
                    <FormField label="Statut" htmlFor="status" required>
                        <Select id="status" name="status" value={form.status} onChange={handleChange} options={STATUS_OPTIONS} />
                    </FormField>
                    <FormField label="Notes / Ordonnance" htmlFor="notes">
                        <Textarea id="notes" name="notes" rows={4} value={form.notes} onChange={handleChange} fullWidth />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Créer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
