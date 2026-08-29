import { useState } from 'react';
import { updateOrganisation } from '../services/organisationsService';
import { CreateOrganisationPayload } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useUpdateOrganisation(
    id: string,
    initialData: CreateOrganisationPayload
) {
    const { showToast } = useToast();
    const [form, setForm] = useState<CreateOrganisationPayload>(initialData);
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [isDirty, setIsDirty] = useState(false); // indique si une modification a eu lieu

    const updateField = <K extends keyof CreateOrganisationPayload>(
        field: K,
        value: CreateOrganisationPayload[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
        setIsDirty(true); // marque comme modifié
    };

    const updateAddress = (
        field: keyof NonNullable<CreateOrganisationPayload['address']>,
        value: string
    ) => {
        setForm((prev) => ({
            ...prev,
            address: {
                ...prev.address,
                [field]: value,
            } as any,
        }));
        setIsDirty(true);
    };

    const setLogo = (file: File | null) => {
        setLogoFile(file);
        setIsDirty(true);
    };

    const submit = async (): Promise<boolean> => {
        // Si rien n'a été modifié, pas de requête
        if (!isDirty) {
            showToast({ type: 'info', message: 'Aucune modification détectée.' });
            return false;
        }

        if (!form.name.trim()) {
            setError('Le nom complet est obligatoire');
            showToast({ type: 'error', message: 'Veuillez remplir les champs obligatoires.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await updateOrganisation(id, form, logoFile);
            showToast({ type: 'success', message: 'Organisation mise à jour avec succès.' });
            setIsDirty(false); // réinitialiser après succès
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la mise à jour.';
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
        isDirty, // exposé si besoin
    };
}
