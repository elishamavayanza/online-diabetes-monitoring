import { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FoodOption } from '../types';

interface MealItemFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    foods: FoodOption[];
    mealId: string;
    onSuccess: (data: { foodId: string; portionGrams: string; breadUnits?: string }) => void;
    isSubmitting?: boolean;
}

export function MealItemFormModal({ isOpen, onClose, foods, mealId, onSuccess, isSubmitting = false }: MealItemFormModalProps) {
    const [foodId, setFoodId] = useState('');
    const [portionGrams, setPortionGrams] = useState('100');
    const [breadUnits, setBreadUnits] = useState('');

    useEffect(() => {
        if (isOpen) {
            setFoodId('');
            setPortionGrams('100');
            setBreadUnits('');
        }
    }, [isOpen]);

    const foodOptions = foods.map((f) => ({ value: f.id, label: f.name }));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSuccess({ foodId, portionGrams, breadUnits: breadUnits || undefined });
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Ajouter un aliment">
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label="Aliment *">
                    <Select value={foodId} onChange={(e) => setFoodId(e.target.value)} options={foodOptions} required />
                </FormField>
                <FormField label="Portion (g) *">
                    <Input type="number" step="0.01" min="0" value={portionGrams} onChange={(e) => setPortionGrams(e.target.value)} required />
                </FormField>
                <FormField label="Unités pain">
                    <Input type="number" step="0.01" min="0" value={breadUnits} onChange={(e) => setBreadUnits(e.target.value)} />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Ajout...' : 'Ajouter'}</Button>
                </div>
            </form>
        </Modal>
    );
}
