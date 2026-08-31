import { useState, useEffect } from 'react';
import { updateMedication } from '../services/medicationsService';
import { MedicationFormValues } from '../types/types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useUpdateMedication(initialData: MedicationFormValues, medicationId: string) {
    const { showToast } = useToast();
    const [form, setForm] = useState<MedicationFormValues>(initialData);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setForm(initialData);
    }, [initialData]);

    const updateField = <K extends keyof MedicationFormValues>(field: K, value: MedicationFormValues[K]) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async (): Promise<boolean> => {
        if (!form.name.trim() || !form.category.trim()) {
            showToast({ type: 'error', message: 'Le nom et la catégorie sont obligatoires.' });
            return false;
        }
        setIsSubmitting(true);
        setError(null);
        try {
            await updateMedication(medicationId, form);
            showToast({ type: 'success', message: 'Médicament mis à jour avec succès.' });
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

    return { form, updateField, submit, isSubmitting, error };
}
