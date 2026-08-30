import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { ProfessionalFormValues } from '../types/userForm.types';

interface Props {
    form: ProfessionalFormValues;
    updateField: (field: keyof ProfessionalFormValues, value: any) => void;
    showCredentials?: boolean; // ✅ nouvelle prop
}

export function ProfessionalFormFields({ form, updateField, showCredentials = true }: Props) {
    return (
        <>
            {showCredentials && (
                <>
                    <FormField label="Email *">
                        <Input value={form.email} onChange={(e) => updateField('email', e.target.value)} required />
                    </FormField>
                    <FormField label="Mot de passe *">
                        <Input type="password" value={form.password} onChange={(e) => updateField('password', e.target.value)} required />
                    </FormField>
                </>
            )}

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
                        { value: 'OTHER', label: 'Autre' },
                    ]}
                />
            </FormField>
            <FormField label="Locale">
                <Input value={form.locale} onChange={(e) => updateField('locale', e.target.value)} />
            </FormField>
            <FormField label="Numéro de licence *">
                <Input value={form.licenseNumber} onChange={(e) => updateField('licenseNumber', e.target.value)} required />
            </FormField>
            <FormField label="Type professionnel">
                <Select
                    value={form.professionalType}
                    onChange={(e) => updateField('professionalType', e.target.value)}
                    options={[
                        { value: 'CLINICIAN', label: 'Clinicien' },
                        { value: 'NUTRITIONIST', label: 'Nutritionniste' },
                    ]}
                />
            </FormField>
            <FormField label="Spécialité">
                <Input value={form.specialty} onChange={(e) => updateField('specialty', e.target.value)} />
            </FormField>
            <FormField label="Signature URL">
                <Input value={form.signatureUrl} onChange={(e) => updateField('signatureUrl', e.target.value)} />
            </FormField>
        </>
    );
}
