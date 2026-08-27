import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { PatientFormValues } from '../types/userForm.types';

interface Props {
    form: PatientFormValues;
    updateField: (field: keyof PatientFormValues, value: any) => void;
}

export function PatientFormFields({ form, updateField }: Props) {
    return (
        <>
            <FormField label="Email *">
                <Input value={form.email} onChange={(e) => updateField('email', e.target.value)} required />
            </FormField>
            <FormField label="Mot de passe *">
                <Input type="password" value={form.password} onChange={(e) => updateField('password', e.target.value)} required />
            </FormField>
            <FormField label="Nom complet *">
                <Input value={form.fullName} onChange={(e) => updateField('fullName', e.target.value)} required />
            </FormField>
            <FormField label="Téléphone">
                <Input value={form.phone} onChange={(e) => updateField('phone', e.target.value)} />
            </FormField>
            <FormField label="Genre">
                <Select
                    value={form.gender}
                    onChange={(e) => updateField('gender', e.target.value)}
                    options={[
                        { value: 'MALE', label: 'Masculin' },
                        { value: 'FEMALE', label: 'Féminin' },
                    ]}
                />
            </FormField>
            <FormField label="Locale">
                <Input value={form.locale} onChange={(e) => updateField('locale', e.target.value)} />
            </FormField>
            <FormField label="Date de naissance">
                <Input type="date" value={form.dateOfBirth} onChange={(e) => updateField('dateOfBirth', e.target.value)} />
            </FormField>
            <FormField label="Lieu de naissance">
                <Input value={form.placeOfBirth} onChange={(e) => updateField('placeOfBirth', e.target.value)} />
            </FormField>
            <FormField label="Groupe sanguin">
                <Input value={form.bloodType} onChange={(e) => updateField('bloodType', e.target.value)} />
            </FormField>
            <FormField label="Taille (cm)">
                <Input value={form.heightCm} onChange={(e) => updateField('heightCm', e.target.value)} />
            </FormField>
        </>
    );
}
