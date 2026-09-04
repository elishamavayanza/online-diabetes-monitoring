import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Textarea } from '@/react/components/Forms/Textarea';
import { MealType } from '../../types';

interface MealFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: (data: { name: string; description?: string; mealType: MealType }) => void;
    isSubmitting?: boolean;
}

const MEAL_TYPE_OPTIONS = [
    { value: 'BREAKFAST', label: 'Petit-déjeuner' },
    { value: 'LUNCH', label: 'Déjeuner' },
    { value: 'DINNER', label: 'Dîner' },
    { value: 'SNACK', label: 'Collation' },
];

export function MealFormModal({ isOpen, onClose, onSuccess, isSubmitting = false }: MealFormModalProps) {
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [mealType, setMealType] = useState<MealType>('LUNCH');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSuccess({ name, description, mealType });
        setName('');
        setDescription('');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouveau repas">
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label="Nom *">
                    <Input value={name} onChange={(e) => setName(e.target.value)} required />
                </FormField>
                <FormField label="Type *">
                    <Select
                        value={mealType}
                        onChange={(e) => setMealType(e.target.value as MealType)}
                        options={MEAL_TYPE_OPTIONS}
                        required
                    />
                </FormField>
                <FormField label="Description">
                    <Textarea value={description} onChange={(e) => setDescription(e.target.value)} />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Création...' : 'Créer'}</Button>
                </div>
            </form>
        </Modal>
    );
}
