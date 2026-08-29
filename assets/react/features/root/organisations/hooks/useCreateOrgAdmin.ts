import { useState } from 'react';
import { createOrgAdmin } from '../services/orgAdminService';
import { OrgAdminFormValues } from '../types/orgAdmin';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext'; // ✅

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

export function useCreateOrgAdmin(organizationId: string) {
    const { showToast } = useToast();
    const [form, setForm] = useState<OrgAdminFormValues>(initialForm);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof OrgAdminFormValues>(
        field: K,
        value: OrgAdminFormValues[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (
        field: keyof OrgAdminFormValues['address'],
        value: string
    ) => {
        setForm((prev) => ({
            ...prev,
            address: {
                ...prev.address,
                [field]: value,
            },
        }));
    };

    const submit = async (): Promise<boolean> => {
        if (!form.fullName.trim() || !form.email.trim() || !form.password.trim()) {
            setError('Veuillez remplir tous les champs obligatoires.');
            showToast({ type: 'error', message: 'Champs obligatoires manquants.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await createOrgAdmin(organizationId, form);
            showToast({ type: 'success', message: 'Administrateur créé avec succès.' });
            setForm(initialForm);
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, submit, isSubmitting, error };
}
