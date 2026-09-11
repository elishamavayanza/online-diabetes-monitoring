import React, { useState } from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateFood } from '../hooks/useCreateFood';
import { FoodCategory } from '../types';
import { FoodPhotoField } from './FoodPhotoField';
import { FoodCategoryFormModal } from './FoodCategoryFormModal';

interface FoodFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: () => void;
    categories: FoodCategory[];
    onCategoryCreated: (category: FoodCategory) => void;
}

export function FoodFormModal({ isOpen, onClose, onSuccess, categories, onCategoryCreated }: FoodFormModalProps) {
    const { t } = useI18n();
    const { form, updateField, setPhotoFile, submit, isSubmitting, error, reset } = useCreateFood();
    const [isCategoryModalOpen, setIsCategoryModalOpen] = useState(false);

    const categoryOptions = categories.map((c) => ({ value: c.id, label: c.label }));

    const handleClose = () => {
        reset();
        onClose();
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            handleClose();
        }
    };

    const handleCategoryCreated = (newCategory: FoodCategory) => {
        onCategoryCreated(newCategory);
        // Sélectionner automatiquement la nouvelle catégorie
        updateField('categoryId', newCategory.id);
    };

    return (
        <Modal isOpen={isOpen} onClose={handleClose} title={t('Ajouter un aliment')}>
            <div className="food-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="food-form-grid">
                        <FormField label={t('Nom *')}>
                            <Input
                                value={form.name}
                                onChange={(e) => updateField('name', e.target.value)}
                                placeholder={t('Ex: Pomme')}
                                required
                            />
                        </FormField>
                        <FormField label={t('Catégorie *')}>
                            <div className="food-category-select-container">
                                <Select
                                    value={form.categoryId}
                                    onChange={(e) => updateField('categoryId', e.target.value)}
                                    options={[{ value: '', label: t('Sélectionner...') }, ...categoryOptions]}
                                    required
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setIsCategoryModalOpen(true)}
                                    title={t('Créer une nouvelle catégorie')}
                                    aria-label={t('Créer une nouvelle catégorie')}
                                    className="food-category-add-btn"
                                >
                                    +
                                </Button>
                            </div>
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
                        <Textarea
                            value={form.description}
                            onChange={(e) => updateField('description', e.target.value)}
                            placeholder={t('Description optionnelle...')}
                        />
                    </FormField>
                    <FoodPhotoField
                        photoUrl={form.photoUrl}
                        onPhotoUrlChange={(url) => updateField('photoUrl', url)}
                        onPhotoFileChange={setPhotoFile}
                    />
                    <div className="food-form-modal__actions">
                        <Button type="button" variant="outline" onClick={handleClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Création...') : t('Créer')}
                        </Button>
                    </div>
                </Form>
            </div>

            {/* Modale de création de catégorie */}
            <FoodCategoryFormModal
                isOpen={isCategoryModalOpen}
                onClose={() => setIsCategoryModalOpen(false)}
                onSuccess={handleCategoryCreated}
            />
        </Modal>
    );
}
