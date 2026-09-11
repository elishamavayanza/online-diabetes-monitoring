import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { ProfessionalFormValues } from '../types/userForm.types';
import { useI18n } from '@/react/i18n/I18nContext';

interface Props {
    form: ProfessionalFormValues;
    updateField: (field: keyof ProfessionalFormValues, value: any) => void;
    showCredentials?: boolean; // ✅ nouvelle prop
}

export function ProfessionalFormFields({ form, updateField, showCredentials = true }: Props) {
    const { t } = useI18n();
    return (
        <>
            {showCredentials && (
                <>
                    <FormField label={t('Email *')}>
                        <Input value={form.email} onChange={(e) => updateField('email', e.target.value)} required />
                    </FormField>
                    <FormField label={t('Mot de passe *')}>
                        <Input type="password" value={form.password} onChange={(e) => updateField('password', e.target.value)} required />
                    </FormField>
                </>
            )}

            <FormField label={t('Nom complet *')}>
                <Input value={form.fullName} onChange={(e) => updateField('fullName', e.target.value)} required />
            </FormField>
            <FormField label={t('Téléphone')}>
                <Input value={form.phone} onChange={(e) => updateField('phone', e.target.value)} />
            </FormField>
            <FormField label={t('Genre')}>
                <Select
                    value={form.gender}
                    onChange={(e) => updateField('gender', e.target.value)}
                    options={[
                        { value: 'MALE', label: t('Masculin') },
                        { value: 'FEMALE', label: t('Féminin') },
                        { value: 'OTHER', label: t('Autre') },
                    ]}
                />
            </FormField>
            <FormField label={t('Locale')}>
                <Input value={form.locale} onChange={(e) => updateField('locale', e.target.value)} />
            </FormField>
            <FormField label={t('Numéro de licence *')}>
                <Input value={form.licenseNumber} onChange={(e) => updateField('licenseNumber', e.target.value)} required />
            </FormField>
            <FormField label={t('Type professionnel')}>
                <Select
                    value={form.professionalType}
                    onChange={(e) => updateField('professionalType', e.target.value)}
                    options={[
                        { value: 'CLINICIAN', label: t('Clinicien') },
                        { value: 'NUTRITIONIST', label: t('Nutritionniste') },
                    ]}
                />
            </FormField>
            <FormField label={t('Spécialité')}>
                <Input value={form.specialty} onChange={(e) => updateField('specialty', e.target.value)} />
            </FormField>
            <FormField label={t('Signature URL')}>
                <Input value={form.signatureUrl} onChange={(e) => updateField('signatureUrl', e.target.value)} />
            </FormField>
        </>
    );
}
