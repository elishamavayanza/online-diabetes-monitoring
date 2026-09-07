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
import { useUpdateMedication } from '../hooks/useUpdateMedication';
import { MedicationFormValues, MedicationClass, MedicationForm } from '../types/types';

interface MedicationEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    medicationId: string;
    medicationData: MedicationFormValues;
    onSuccess?: () => void;
}

const CATEGORY_OPTIONS = [
    { value: 'INSULIN', label: 'Insuline (antidiabétique)' },
    { value: 'GENERAL', label: 'Médicament général' },
];

const FORM_OPTIONS = [
    { value: 'TABLET', label: 'Comprimé' },
    { value: 'LIQUID', label: 'Liquide' },
];

const INSULIN_TYPE_OPTIONS = [
    { value: 'RAPID_ACTING', label: 'Action rapide' },
    { value: 'SHORT_ACTING', label: 'Action courte' },
    { value: 'INTERMEDIATE_ACTING', label: 'Action intermédiaire' },
    { value: 'LONG_ACTING', label: 'Action longue' },
    { value: 'MIXED', label: 'Prémélangée' },
    { value: 'OTHER', label: 'Autre' },
];

export function MedicationEditModal({ isOpen, onClose, medicationId, medicationData, onSuccess }: MedicationEditModalProps) {
    const { form, updateField, submit, isSubmitting, error } = useUpdateMedication(medicationData, medicationId);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Modifier le médicament">
            <div className="medication-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="medication-form-grid">
                        <FormField label="Nom commercial *">
                            <Input value={form.name} onChange={(e) => updateField('name', e.target.value)} required />
                        </FormField>
                        <FormField label="Classe *">
                            <Select
                                value={form.category}
                                onChange={(e) => updateField('category', e.target.value as MedicationClass)}
                                options={CATEGORY_OPTIONS}
                                required
                            />
                        </FormField>
                        {form.category === 'INSULIN' && (
                            <>
                                <FormField label="Type d'insuline *">
                                    <Select
                                        value={form.insulinType ?? ''}
                                        onChange={(e) => updateField('insulinType', e.target.value)}
                                        options={INSULIN_TYPE_OPTIONS}
                                        required
                                    />
                                </FormField>
                                <FormField label="Concentration *">
                                    <Input
                                        value={form.concentration ?? ''}
                                        onChange={(e) => updateField('concentration', e.target.value)}
                                        placeholder="Ex: U-100"
                                        required
                                    />
                                </FormField>
                            </>
                        )}
                        {form.category === 'GENERAL' && (
                            <FormField label="Forme *">
                                <Select
                                    value={form.form ?? 'TABLET'}
                                    onChange={(e) => updateField('form', e.target.value as MedicationForm)}
                                    options={FORM_OPTIONS}
                                    required
                                />
                            </FormField>
                        )}
                        <FormField label="Fabricant">
                            <Input value={form.manufacturer ?? ''} onChange={(e) => updateField('manufacturer', e.target.value)} />
                        </FormField>
                    </div>
                    {form.category === 'GENERAL' && form.form === 'LIQUID' && (
                        <p className="medication-form-modal__hint">
                            Forme liquide : sirop, suspension ou solution buvable — le dosage en prescription s’exprimera en volume (mL).
                        </p>
                    )}
                    <FormField label="Description">
                        <Textarea value={form.description ?? ''} onChange={(e) => updateField('description', e.target.value)} />
                    </FormField>
                    <FormField label="Actif">
                        <Switch checked={form.active ?? true} onChange={(e) => updateField('active', e.target.checked)} />
                    </FormField>
                    <div className="medication-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Enregistrement...' : 'Enregistrer'}</Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
