import { useState } from 'react';
import { createOrgAdmin } from '../services/orgAdminService';
import { OrgAdminFormValues, Gender } from '../types/orgAdmin';

const initialForm: OrgAdminFormValues = {
    email: '',
    password: '',
    fullName: '',
    gender: 'UNSPECIFIED',
    phone: '',
    locale: 'fr',
    avatarUrl: '',
    address: {
        street: '',
        city: '',
        postalCode: '',
        country: 'RDC',
    },
};

export function useCreateOrgAdmin() {
    const [form, setForm] = useState<OrgAdminFormValues>(initialForm);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof OrgAdminFormValues>(
        field: K,
        value: OrgAdminFormValues[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof OrgAdminFormValues['address'], value: string) => {
        setForm((prev) => ({
            ...prev,
            address: {
                ...prev.address,
                [field]: value,
            },
        }));
    };

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await createOrgAdmin(form);
            setForm(initialForm);
        } catch (err) {
            setError('Erreur lors de la création.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, submit, isSubmitting, error };
}
