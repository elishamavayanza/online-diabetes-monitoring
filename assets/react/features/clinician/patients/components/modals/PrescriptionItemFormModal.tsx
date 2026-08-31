import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createPrescriptionItem } from '../../services/dossierActionsService';
import { fetchMedications } from '@/react/features/admin/medications/services/medicationsService';
import { PatientPrescription } from '../../types';

interface PrescriptionItemFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    prescription: PatientPrescription | null;
    onSuccess: () => void;
}

export function PrescriptionItemFormModal({ isOpen, onClose, prescription, onSuccess }: PrescriptionItemFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [medications, setMedications] = useState<{ value: string; label: string }[]>([]);
    const [form, setForm] = useState({
        medicationId: '',
        dosage: '',
        quantity: '1.00',
        morning: true,
        noon: false,
        evening: true,
        instructions: '',
    });

    useEffect(() => {
        if (isOpen) {
            setForm({
                medicationId: '',
                dosage: '',
                quantity: '1.00',
                morning: true,
                noon: false,
                evening: true,
                instructions: '',
            });
            setError(null);
            fetchMedications()
                .then((list) => setMedications(list.map((m) => ({ value: m.id, label: m.name }))))
                .catch(() => setMedications([]));
        }
    }, [isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        const { name, value, type } = e.target;
        if (type === 'checkbox') {
            const checked = (e.target as HTMLInputElement).checked;
            setForm((prev) => ({ ...prev, [name]: checked }));
        } else {
            setForm((prev) => ({ ...prev, [name]: value }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!prescription) return;
        setIsLoading(true);
        setError(null);
        try {
            await createPrescriptionItem({
                prescriptionId: prescription.id,
                medicationId: form.medicationId,
                dosage: form.dosage,
                quantity: form.quantity,
                morning: form.morning,
                noon: form.noon,
                evening: form.evening,
                instructions: form.instructions || undefined,
            });
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de l\'ajout.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Ajouter un médicament">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Médicament" htmlFor="medicationId" required>
                        <Select
                            id="medicationId"
                            name="medicationId"
                            value={form.medicationId}
                            onChange={handleChange}
                            options={medications}
                            placeholder="Sélectionner un médicament"
                        />
                    </FormField>
                    <FormField label="Posologie" htmlFor="dosage" required>
                        <Input id="dosage" name="dosage" value={form.dosage} onChange={handleChange} placeholder="ex: 1 comprimé" required />
                    </FormField>
                    <FormField label="Quantité" htmlFor="quantity" required>
                        <Input id="quantity" name="quantity" value={form.quantity} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Prises">
                        <div className="dossier-form__checkboxes">
                            <label><input type="checkbox" name="morning" checked={form.morning} onChange={handleChange} /> Matin</label>
                            <label><input type="checkbox" name="noon" checked={form.noon} onChange={handleChange} /> Midi</label>
                            <label><input type="checkbox" name="evening" checked={form.evening} onChange={handleChange} /> Soir</label>
                        </div>
                    </FormField>
                    <FormField label="Instructions" htmlFor="instructions">
                        <Textarea id="instructions" name="instructions" rows={3} value={form.instructions} onChange={handleChange} fullWidth />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading || !form.medicationId}>
                        {isLoading ? <Spinner size="small" /> : 'Ajouter'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
