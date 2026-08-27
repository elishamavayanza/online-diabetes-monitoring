import { useState } from 'react';
import { createAffectation } from '../services/affectationService';
import { AffectationData } from '../types/affectation';

export function useCreateAffectation(userId: string) {
    const [form, setForm] = useState<AffectationData>({
        userId,
        organizationId: '',
        facilityId: '',
        departmentId: '',
        startDate: '',
        endDate: '',
        status: 'ACTIVE',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = (field: keyof AffectationData, value: any) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await createAffectation(form);
            // Réinitialiser ?
        } catch (err) {
            setError('Erreur lors de la création.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error };
}
