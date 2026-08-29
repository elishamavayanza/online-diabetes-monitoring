import { useState } from 'react';
import { createOrganisation } from '../services/organisationsService';
import { CreateOrganisationPayload } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext'; // ✅ bon import

const initialForm: CreateOrganisationPayload = {
    name: '',
    shortName: '',
    type: 'HOSPITAL',
    email: '',
    phone: '',
    website: '',
    logoUrl: '',
    active: true,
    address: {
        street: '',
        city: '',
        postalCode: '',
        country: 'RDC',
    },
};

export function useCreateOrganisation() {
    const { showToast } = useToast(); // ✅ showToast
    const [form, setForm] = useState<CreateOrganisationPayload>(initialForm);
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof CreateOrganisationPayload>(
        field: K,
        value: CreateOrganisationPayload[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof NonNullable<CreateOrganisationPayload['address']>, value: string) => {
        setForm((prev) => ({
            ...prev,
            address: {
                ...prev.address,
                [field]: value,
            } as any,
        }));
    };

    const setLogo = (file: File | null) => {
        setLogoFile(file);
    };

    const submit = async (): Promise<boolean> => {
        if (!form.name.trim()) {
            setError('Le nom complet est obligatoire');
            showToast({ type: 'error', message: 'Veuillez remplir les champs obligatoires.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await createOrganisation(form, logoFile);
            showToast({ type: 'success', message: 'Organisation créée avec succès.' });
            setForm(initialForm);
            setLogoFile(null);
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

    return {
        form,
        updateField,
        updateAddress,
        submit,
        isSubmitting,
        error,
        setLogo,
    };
}
