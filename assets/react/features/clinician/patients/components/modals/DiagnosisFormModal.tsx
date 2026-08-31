import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createDiagnosis, updateDiagnosis } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDiagnosis, PatientDossierData } from '../../types';

interface DiagnosisFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    diagnosis?: PatientDiagnosis;
    onSuccess: () => void;
}

const STATUS_OPTIONS = [
    { value: 'CONFIRMED', label: 'Confirmé' },
    { value: 'SUSPECTED', label: 'Suspecté' },
    { value: 'RULED_OUT', label: 'Écarté' },
    { value: 'IN_REMISSION', label: 'En rémission' },
];

export function DiagnosisFormModal({ isOpen, onClose, data, diagnosis, onSuccess }: DiagnosisFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        conditionName: '',
        description: '',
        diagnosedAt: '',
        status: 'CONFIRMED',
    });

    const isEdit = !!diagnosis;

    useEffect(() => {
        if (isOpen) {
            setForm({
                conditionName: diagnosis?.conditionName ?? '',
                description: diagnosis?.description ?? '',
                diagnosedAt: diagnosis?.diagnosedAt
                    ? new Date(diagnosis.diagnosedAt).toISOString().slice(0, 16)
                    : new Date().toISOString().slice(0, 16),
                status: diagnosis?.status ?? 'CONFIRMED',
            });
            setError(null);
        }
    }, [isOpen, diagnosis]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const doctorId = getCurrentUserIdFromToken();
        if (!doctorId) {
            setError('Impossible d\'identifier le médecin.');
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                doctorId,
                conditionName: form.conditionName,
                description: form.description || undefined,
                diagnosedAt: new Date(form.diagnosedAt).toISOString(),
                status: form.status,
                medicalRecordId: data.record?.id,
            };
            if (isEdit && diagnosis) {
                await updateDiagnosis(diagnosis.id, payload);
            } else {
                await createDiagnosis(payload);
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
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? 'Modifier le diagnostic' : 'Ajouter un diagnostic'}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Affection" htmlFor="conditionName" required>
                        <Input id="conditionName" name="conditionName" value={form.conditionName} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Statut" htmlFor="status" required>
                        <Select id="status" name="status" value={form.status} onChange={handleChange} options={STATUS_OPTIONS} />
                    </FormField>
                    <FormField label="Date du diagnostic" htmlFor="diagnosedAt" required>
                        <Input id="diagnosedAt" name="diagnosedAt" type="datetime-local" value={form.diagnosedAt} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Description" htmlFor="description">
                        <Textarea id="description" name="description" rows={4} value={form.description} onChange={handleChange} fullWidth />
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
