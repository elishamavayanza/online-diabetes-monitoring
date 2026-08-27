import { useState } from 'react';
import { updateAffectation } from '../services/affectationService';
import { AffectationData } from '../types/affectation';

export function useUpdateAffectation(initialData: AffectationData) {
    const [form, setForm] = useState<AffectationData>(initialData);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = (field: keyof AffectationData, value: any) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async (affectationId: string) => {
        setIsSubmitting(true);
        setError(null);
        try {
            await updateAffectation(affectationId, form);
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error };
}
