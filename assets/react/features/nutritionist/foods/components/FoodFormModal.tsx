import React from 'react';
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

interface FoodFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: () => void;
    categories: FoodCategory[];
}

export function FoodFormModal({ isOpen, onClose, onSuccess, categories }: FoodFormModalProps) {
    const { form, updateField, setPhotoFile, submit, isSubmitting, error, reset } = useCreateFood();

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

    return (
        <Modal isOpen={isOpen} onClose={handleClose} title="Ajouter un aliment">
            <div className="food-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="food-form-grid">
                        <FormField label="Nom *">
                            <Input
                                value={form.name}
                                onChange={(e) => updateField('name', e.target.value)}
                                placeholder="Ex: Pomme"
                                required
                            />
                        </FormField>
                        <FormField label="Catégorie *">
                            <Select
                                value={form.categoryId}
                                onChange={(e) => updateField('categoryId', e.target.value)}
                                options={[{ value: '', label: 'Sélectionner...' }, ...categoryOptions]}
                                required
                            />
                        </FormField>
                        <FormField label="Calories (kcal/100g) *">
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.caloriesPer100g}
                                onChange={(e) => updateField('caloriesPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label="Glucides (g/100g) *">
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.carbsPer100g}
                                onChange={(e) => updateField('carbsPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label="Protéines (g/100g) *">
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.proteinPer100g}
                                onChange={(e) => updateField('proteinPer100g', e.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label="Lipides (g/100g) *">
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
                    <FormField label="Description">
                        <Textarea
                            value={form.description}
                            onChange={(e) => updateField('description', e.target.value)}
                            placeholder="Description optionnelle..."
                        />
                    </FormField>
                    <FoodPhotoField
                        photoUrl={form.photoUrl}
                        onPhotoUrlChange={(url) => updateField('photoUrl', url)}
                        onPhotoFileChange={setPhotoFile}
                    />
                    <div className="food-form-modal__actions">
                        <Button type="button" variant="outline" onClick={handleClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Création...' : 'Créer'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
