import { useState } from 'react';
import { createFood, uploadFoodPhoto } from '../services/foodsService';
import { FoodFormValues } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

const initialForm: FoodFormValues = {
    categoryId: '',
    name: '',
    description: '',
    photoUrl: '',
    caloriesPer100g: '0.00',
    carbsPer100g: '0.00',
    proteinPer100g: '0.00',
    fatPer100g: '0.00',
};

export function useCreateFood() {
    const { showToast } = useToast();
    const [form, setForm] = useState<FoodFormValues>(initialForm);
    const [photoFile, setPhotoFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof FoodFormValues>(field: K, value: FoodFormValues[K]) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const reset = () => {
        setForm(initialForm);
        setPhotoFile(null);
        setError(null);
    };

    const submit = async (): Promise<boolean> => {
        if (!form.name.trim() || !form.categoryId) {
            showToast({ type: 'error', message: 'Le nom et la catégorie sont obligatoires.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            const payload = { ...form };
            if (photoFile) {
                payload.photoUrl = await uploadFoodPhoto(photoFile);
            }
            await createFood(payload);
            showToast({ type: 'success', message: 'Aliment créé avec succès.' });
            reset();
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

    return { form, updateField, photoFile, setPhotoFile, submit, isSubmitting, error, reset };
}
