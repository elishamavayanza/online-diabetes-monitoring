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
import { MedicationClass, MedicationForm } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

interface MedicationFormModalProps {
    isOpen: boolean;
    onClose: () => void;
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

export function MedicationFormModal({ isOpen, onClose, onSuccess }: MedicationFormModalProps) {
    const { form, updateField, submit, isSubmitting, error } = useCreateMedication();
    const { t } = useI18n();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Ajouter un médicament')}>
            <div className="medication-form-modal">
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <div className="medication-form-grid">
                        <FormField label={t('Nom commercial *')}>
                            <Input
                                value={form.name}
                                onChange={(e) => updateField('name', e.target.value)}
                                placeholder={t('Ex: Metformine 500 mg')}
                                required
                            />
                        </FormField>
                        <FormField label={t('Classe *')}>
                            <Select
                                value={form.category}
                                onChange={(e) => updateField('category', e.target.value as MedicationClass)}
                                options={CATEGORY_OPTIONS.map(o => ({ ...o, label: t(o.label) }))}
                                required
                            />
                        </FormField>
                        {form.category === 'INSULIN' && (
                            <>
                                <FormField label={t("Type d'insuline *")}>
                                    <Select
                                        value={form.insulinType ?? ''}
                                        onChange={(e) => updateField('insulinType', e.target.value)}
                                        options={INSULIN_TYPE_OPTIONS.map(o => ({ ...o, label: t(o.label) }))}
                                        placeholder={t('Sélectionnez un type')}
                                        required
                                    />
                                </FormField>
                                <FormField label={t('Concentration *')}>
                                    <Input
                                        value={form.concentration ?? ''}
                                        onChange={(e) => updateField('concentration', e.target.value)}
                                        placeholder={t('Ex: U-100')}
                                        required
                                    />
                                </FormField>
                            </>
                        )}
                        {form.category === 'GENERAL' && (
                            <FormField label={t('Forme *')}>
                                <Select
                                    value={form.form ?? 'TABLET'}
                                    onChange={(e) => updateField('form', e.target.value as MedicationForm)}
                                    options={FORM_OPTIONS.map(o => ({ ...o, label: t(o.label) }))}
                                    required
                                />
                            </FormField>
                        )}
                        <FormField label={t('Fabricant')}>
                            <Input
                                value={form.manufacturer ?? ''}
                                onChange={(e) => updateField('manufacturer', e.target.value)}
                                placeholder="Ex: PharmaLab"
                            />
                        </FormField>
                    </div>
                    {form.category === 'GENERAL' && form.form === 'LIQUID' && (
                        <p className="medication-form-modal__hint">
                            {t('Forme liquide : sirop, suspension ou solution buvable — le dosage en prescription s\u2019exprimera en volume (mL).')}
                        </p>
                    )}
                    <FormField label={t('Description')}>
                        <Textarea
                            value={form.description ?? ''}
                            onChange={(e) => updateField('description', e.target.value)}
                            placeholder={
                                form.category === 'GENERAL' && form.form === 'LIQUID'
                                    ? 'Ex: Sirop buvable — 1 cuillère à soupe matin et soir...'
                                    : 'Ex: Antidiabétique oral de la classe des biguanides...'
                            }
                        />
                    </FormField>
                    <FormField label={t('Actif')}>
                        <Switch checked={form.active ?? true} onChange={(e) => updateField('active', e.target.checked)} />
                    </FormField>
                    <div className="medication-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>{isSubmitting ? t('Création...') : t('Créer')}</Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
