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
            showToast({ type: 'error', message: 'Le nom et la classe sont obligatoires.' });
            return false;
        }
        if (form.category === 'INSULIN' && !form.insulinType) {
            showToast({ type: 'error', message: "Sélectionnez le type d'insuline." });
            return false;
        }
        if (form.category === 'INSULIN' && !form.concentration?.trim()) {
            showToast({ type: 'error', message: "Saisissez la concentration de l'insuline." });
            return false;
        }
        if (form.category === 'GENERAL' && !form.form) {
            showToast({ type: 'error', message: 'La forme (comprimé ou liquide) est obligatoire pour un médicament général.' });
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
