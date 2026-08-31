import { useState } from 'react';
import { createMedication } from '../services/medicationsService';
import { MedicationFormValues } from '../types/types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

const initialForm: MedicationFormValues = {
    name: '',
    category: 'TABLET',
    description: '',
    insulinLevel: 0,
    manufacturer: '',
    active: true,
};

export function useCreateMedication() {
    const { showToast } = useToast();
    const [form, setForm] = useState<MedicationFormValues>(initialForm);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

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
            await createMedication(form);
            showToast({ type: 'success', message: 'Médicament créé avec succès.' });
            setForm(initialForm);
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

    return { form, updateField, submit, isSubmitting, error };
}
