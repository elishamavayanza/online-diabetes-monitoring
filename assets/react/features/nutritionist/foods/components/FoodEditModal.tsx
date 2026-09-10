import React from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateFood } from '../hooks/useUpdateFood';
import { FoodCategory, FoodFormValues } from '../types';
import { FoodPhotoField } from './FoodPhotoField';

interface FoodEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    foodId: string;
    foodData: FoodFormValues;
    categories: FoodCategory[];
    onSuccess?: () => void;
}

export function FoodEditModal({ isOpen, onClose, foodId, foodData, categories, onSuccess }: FoodEditModalProps) {
    const { t } = useI18n();
    const { form, updateField, setPhotoFile, submit, isSubmitting, error } = useUpdateFood(foodData, foodId);

    const categoryOptions = categories.map((c) => ({ value: c.id, label: c.label }));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t("Modifier l'aliment")}>
            <div className="food-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="food-form-grid">
                        <FormField label={t('Nom *')}>
                            <Input value={form.name} onChange={(e) => updateField('name', e.target.value)} required />
                        </FormField>
                        <FormField label={t('Catégorie *')}>
                            <Select
                                value={form.categoryId}
                                onChange={(e) => updateField('categoryId', e.target.value)}
                                options={categoryOptions}
                                required
                            />
                        </FormField>
                        <FormField label={t('Calories (kcal/100g) *')}>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.caloriesPer100g}
                                onChange={(e) => updateField('caloriesPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label={t('Glucides (g/100g) *')}>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.carbsPer100g}
                                onChange={(e) => updateField('carbsPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label={t('Protéines (g/100g) *')}>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.proteinPer100g}
                                onChange={(e) => updateField('proteinPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label={t('Lipides (g/100g) *')}>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.fatPer100g}
                                onChange={(e) => updateField('fatPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                    </div>
                    <FormField label={t('Description')}>
                        <Textarea value={form.description} onChange={(e) => updateField('description', e.target.value)} />
                    </FormField>
                    <FoodPhotoField
                        photoUrl={form.photoUrl}
                        onPhotoUrlChange={(url) => updateField('photoUrl', url)}
                        onPhotoFileChange={setPhotoFile}
                    />
                    <div className="food-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Enregistrement...') : t('Enregistrer')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
