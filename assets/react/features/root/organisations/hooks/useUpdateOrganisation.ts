// hooks/useUpdateOrganisation.ts
import { useState } from 'react';
import { updateOrganisation } from '../services/organisationsService';
import { CreateOrganisationPayload } from '../types';

export function useUpdateOrganisation(initialData: CreateOrganisationPayload) {
    const [form, setForm] = useState<CreateOrganisationPayload>(initialData);
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

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await updateOrganisation(form);
            // Après succès, vous pouvez appeler une fonction de rappel pour rafraîchir la liste
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
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
    };
}
