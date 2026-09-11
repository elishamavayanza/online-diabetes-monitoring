import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Textarea } from '@/react/components/Forms/Textarea';
import { MealType } from '../../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface MealFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: (data: { name: string; description?: string; mealType: MealType }) => void;
    isSubmitting?: boolean;
}

const MEAL_TYPE_VALUES = ['BREAKFAST', 'LUNCH', 'DINNER', 'SNACK'] as const;

export function MealFormModal({ isOpen, onClose, onSuccess, isSubmitting = false }: MealFormModalProps) {
    const { t } = useI18n();
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [mealType, setMealType] = useState<MealType>('LUNCH');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSuccess({ name, description, mealType });
        setName('');
        setDescription('');
    };

    const mealTypeOptions = MEAL_TYPE_VALUES.map((value) => ({
        value,
        label: t(value === 'BREAKFAST' ? 'Petit-déjeuner' : value === 'LUNCH' ? 'Déjeuner' : value === 'DINNER' ? 'Dîner' : 'Collation'),
    }));

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Nouveau repas')}>
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label={t('Nom *')}>
                    <Input value={name} onChange={(e) => setName(e.target.value)} required />
                </FormField>
                <FormField label={t('Type *')}>
                    <Select
                        value={mealType}
                        onChange={(e) => setMealType(e.target.value as MealType)}
                        options={mealTypeOptions}
                        required
                    />
                </FormField>
                <FormField label={t('Description')}>
                    <Textarea value={description} onChange={(e) => setDescription(e.target.value)} />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>{t('Annuler')}</Button>
                    <Button type="submit" disabled={isSubmitting}>{isSubmitting ? t('Création...') : t('Créer')}</Button>
                </div>
            </form>
        </Modal>
    );
}
