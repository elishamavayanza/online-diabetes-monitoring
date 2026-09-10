import { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FoodOption } from '../../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface MealItemFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    foods: FoodOption[];
    mealId: string;
    onSuccess: (data: { foodId: string; portionGrams: string; breadUnits?: string }) => void;
    isSubmitting?: boolean;
}

export function MealItemFormModal({ isOpen, onClose, foods, mealId, onSuccess, isSubmitting = false }: MealItemFormModalProps) {
    const { t } = useI18n();
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
        <Modal isOpen={isOpen} onClose={onClose} title={t('Ajouter un aliment')}>
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label={t('Aliment *')}>
                    <Select value={foodId} onChange={(e) => setFoodId(e.target.value)} options={foodOptions} required />
                </FormField>
                <FormField label={t('Portion (g) *')}>
                    <Input type="number" step="0.01" min="0" value={portionGrams} onChange={(e) => setPortionGrams(e.target.value)} required />
                </FormField>
                <FormField label={t('Unités pain')}>
                    <Input type="number" step="0.01" min="0" value={breadUnits} onChange={(e) => setBreadUnits(e.target.value)} />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>{t('Annuler')}</Button>
                    <Button type="submit" disabled={isSubmitting}>{isSubmitting ? t('Ajout...') : t('Ajouter')}</Button>
                </div>
            </form>
        </Modal>
    );
}
