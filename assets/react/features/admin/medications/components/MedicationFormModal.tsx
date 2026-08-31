import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateMedication } from '../hooks/useCreateMedication';

interface MedicationFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: () => void;
}

const CATEGORY_OPTIONS = [
    { value: 'INSULIN', label: 'Insuline' },
    { value: 'TABLET', label: 'Comprimé' },
    { value: 'OTHER', label: 'Autre' },
];

export function MedicationFormModal({ isOpen, onClose, onSuccess }: MedicationFormModalProps) {
    const { form, updateField, submit, isSubmitting, error } = useCreateMedication();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Ajouter un médicament">
            <div className="medication-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="medication-form-grid">
                        <FormField label="Nom commercial *">
                            <Input
                                value={form.name}
                                onChange={(e) => updateField('name', e.target.value)}
                                placeholder="Ex: Metformine 500 mg"
                                required
                            />
                        </FormField>
                        <FormField label="Catégorie *">
                            <Select
                                value={form.category}
                                onChange={(e) => updateField('category', e.target.value)}
                                options={CATEGORY_OPTIONS}
                                required
                            />
                        </FormField>
                        <FormField label="Fabricant">
                            <Input
                                value={form.manufacturer ?? ''}
                                onChange={(e) => updateField('manufacturer', e.target.value)}
                                placeholder="Ex: PharmaLab"
                            />
                        </FormField>
                        <FormField label="Niveau d'insuline">
                            <Input
                                type="number"
                                value={form.insulinLevel ?? 0}
                                onChange={(e) => updateField('insulinLevel', Number(e.target.value))}
                                placeholder="0"
                                min={0}
                            />
                        </FormField>
                    </div>
                    <FormField label="Description">
                        <Textarea
                            value={form.description ?? ''}
                            onChange={(e) => updateField('description', e.target.value)}
                            placeholder="Ex: Antidiabétique oral de la classe des biguanides..."
                        />
                    </FormField>
                    <FormField label="Actif">
                        <Switch checked={form.active ?? true} onChange={(e) => updateField('active', e.target.checked)} />
                    </FormField>
                    <div className="medication-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Création...' : 'Créer'}</Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
