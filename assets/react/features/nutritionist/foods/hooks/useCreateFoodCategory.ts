// hooks/useCreateFoodCategory.ts
import { useState } from 'react';
import { createFoodCategory } from '../services/foodsService';
import { FoodCategory } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseCreateFoodCategoryReturn {
    label: string;
    description: string;
    setLabel: (value: string) => void;
    setDescription: (value: string) => void;
    isSubmitting: boolean;
    error: string | null;
    submit: () => Promise<FoodCategory | null>;
}

export function useCreateFoodCategory(): UseCreateFoodCategoryReturn {
    const { showToast } = useToast();
    const [label, setLabel] = useState('');
    const [description, setDescription] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const submit = async (): Promise<FoodCategory | null> => {
        if (!label.trim()) {
            showToast({ type: 'error', message: 'Le libellé est obligatoire.' });
            return null;
        }
        setIsSubmitting(true);
        setError(null);
        try {
            const category = await createFoodCategory({ label: label.trim(), description: description.trim() || undefined });
            showToast({ type: 'success', message: 'Catégorie créée avec succès.' });
            // Réinitialiser
            setLabel('');
            setDescription('');
            return category;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
            setError(message);
            showToast({ type: 'error', message });
            return null;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { label, description, setLabel, setDescription, isSubmitting, error, submit };
}
