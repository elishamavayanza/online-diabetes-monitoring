import { useState } from 'react';
import { createOrganisation } from '../services/organisationsService';
import { CreateOrganisationPayload, OrganisationType } from '../types';

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
    const [form, setForm] = useState<CreateOrganisationPayload>(initialForm);
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
            await createOrganisation(form);
            // Réinitialiser après succès
            setForm(initialForm);
        } catch (err) {
            setError('Erreur lors de la création.');
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
