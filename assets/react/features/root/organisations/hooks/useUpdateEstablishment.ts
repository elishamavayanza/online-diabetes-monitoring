import { useState } from 'react';
import { updateEstablishment } from '../services/establishmentService';
import { EstablishmentFormValues, Establishment } from '../types/establishment';

export function useUpdateEstablishment(establishment: Establishment) {
    const [form, setForm] = useState<EstablishmentFormValues>({
        organizationId: establishment.organizationId,
        name: establishment.name,
        phone: establishment.phone,
        address: establishment.address,
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof EstablishmentFormValues>(
        field: K,
        value: EstablishmentFormValues[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof EstablishmentFormValues['address'], value: string) => {
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
            await updateEstablishment(establishment.id, form);
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, submit, isSubmitting, error };
}
