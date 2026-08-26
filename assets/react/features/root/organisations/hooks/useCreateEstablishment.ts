import { useState } from 'react';
import { createEstablishment } from '../services/establishmentService';
import { EstablishmentFormValues } from '../types/establishment';

const initialForm: EstablishmentFormValues = {
    organizationId: '',
    name: '',
    phone: '',
    address: {
        street: '',
        city: '',
        postalCode: '',
        country: 'RDC',
    },
};

export function useCreateEstablishment(organizationId: string) {
    const [form, setForm] = useState<EstablishmentFormValues>({
        ...initialForm,
        organizationId,
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
            await createEstablishment(form);
            setForm({ ...initialForm, organizationId });
        } catch (err) {
            setError('Erreur lors de la création.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, submit, isSubmitting, error };
}
