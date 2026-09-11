import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { PatientFormValues } from '../types/userForm.types';
import { useI18n } from '@/react/i18n/I18nContext';

interface Props {
    form: PatientFormValues;
    updateField: (field: keyof PatientFormValues, value: any) => void;
    showCredentials?: boolean;
}

export function PatientFormFields({ form, updateField, showCredentials = true }: Props) {
    const { t } = useI18n();
    return (
        <>
            {showCredentials && (
                <>
                    <FormField label={t('Email *')}>
                        <Input value={form.email} onChange={(e) => updateField('email', e.target.value)} placeholder={t('ex : nom@exemple.com')} required />
                    </FormField>
                    <FormField label={t('Mot de passe *')}>
                        <Input type="password" value={form.password} onChange={(e) => updateField('password', e.target.value)} placeholder={t('8 caractères minimum')} required />
                    </FormField>
                </>
            )}
            <FormField label={t('Nom complet *')}>
                <Input value={form.fullName} onChange={(e) => updateField('fullName', e.target.value)} placeholder={t('ex : Jean Mukendi')} required />
            </FormField>
            <FormField label={t('Téléphone')}>
                <Input value={form.phone} onChange={(e) => updateField('phone', e.target.value)} placeholder={t('ex : +243 990 000 000')} />
            </FormField>
            <FormField label={t('Genre')}>
                <Select
                    value={form.gender}
                    onChange={(e) => updateField('gender', e.target.value)}
                    options={[
                        { value: 'MALE', label: t('Masculin') },
                        { value: 'FEMALE', label: t('Féminin') },
                    ]}
                />
            </FormField>
            <FormField label={t('Locale')}>
                <Input value={form.locale} onChange={(e) => updateField('locale', e.target.value)} />
            </FormField>
            <FormField label={t('Date de naissance')}>
                <Input type="date" value={form.dateOfBirth} onChange={(e) => updateField('dateOfBirth', e.target.value)} />
            </FormField>
            <FormField label={t('Type de diabète')}>
                <Select
                    value={form.diabetesType}
                    onChange={(e) => updateField('diabetesType', e.target.value)}
                    options={[
                        { value: '', label: t('Choisir le type de diabète') },
                        { value: 'TYPE_1', label: t('Type 1') },
                        { value: 'TYPE_2', label: t('Type 2') },
                        { value: 'GESTATIONAL', label: t('Gestationnel') },
                        { value: 'OTHER', label: t('Autre') },
                    ]}
                />
            </FormField>
            <FormField label={t('Lieu de naissance')}>
                <Input value={form.placeOfBirth} onChange={(e) => updateField('placeOfBirth', e.target.value)} />
            </FormField>
            <FormField label={t('Groupe sanguin')}>
                <Input value={form.bloodType} onChange={(e) => updateField('bloodType', e.target.value)} />
            </FormField>
            <FormField label={t('Taille (cm)')}>
                <Input value={form.heightCm} onChange={(e) => updateField('heightCm', e.target.value)} />
            </FormField>
        </>
    );
}
