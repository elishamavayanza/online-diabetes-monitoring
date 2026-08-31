import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createMeal } from '../../services/dossierActionsService';
import { PatientDossierData } from '../../types';

interface MealFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    onSuccess: () => void;
}

const MEAL_TYPE_OPTIONS = [
    { value: 'BREAKFAST', label: 'Petit-déjeuner' },
    { value: 'LUNCH', label: 'Déjeuner' },
    { value: 'DINNER', label: 'Dîner' },
    { value: 'SNACK', label: 'Collation' },
];

export function MealFormModal({ isOpen, onClose, data, onSuccess }: MealFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({ name: '', mealType: 'LUNCH', description: '' });

    useEffect(() => {
        if (isOpen) {
            setForm({ name: '', mealType: 'LUNCH', description: '' });
            setError(null);
        }
    }, [isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setError(null);
        try {
            await createMeal({
                name: form.name,
                mealType: form.mealType,
                description: form.description || undefined,
                patientId: data.profile.id,
            });
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de l\'enregistrement.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Enregistrer un repas">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Nom du repas" htmlFor="name" required>
                        <Input id="name" name="name" value={form.name} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Type" htmlFor="mealType" required>
                        <Select id="mealType" name="mealType" value={form.mealType} onChange={handleChange} options={MEAL_TYPE_OPTIONS} />
                    </FormField>
                    <FormField label="Description" htmlFor="description">
                        <Textarea id="description" name="description" rows={3} value={form.description} onChange={handleChange} fullWidth />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
