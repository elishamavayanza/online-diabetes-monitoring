import { useEffect, useState } from 'react';
import { updateFood, uploadFoodPhoto } from '../services/foodsService';
import { FoodFormValues } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useUpdateFood(initialData: FoodFormValues, foodId: string) {
    const { showToast } = useToast();
    const [form, setForm] = useState<FoodFormValues>(initialData);
    const [photoFile, setPhotoFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setForm(initialData);
        setPhotoFile(null);
    }, [initialData, foodId]);

    const updateField = <K extends keyof FoodFormValues>(field: K, value: FoodFormValues[K]) => {
        setForm((prev) => ({ ...prev, [field]: value }));
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
            await updateFood(foodId, payload);
            showToast({ type: 'success', message: 'Aliment mis à jour avec succès.' });
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

    return { form, updateField, photoFile, setPhotoFile, submit, isSubmitting, error };
}
