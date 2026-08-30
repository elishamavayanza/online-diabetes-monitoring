import { useState, useEffect } from 'react';
import { updateProfessional } from '../services/professionalsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { ProfessionalFormValues } from "@/react/features/root/users/types/userForm.types";

export function useUpdateProfessional(
    initialData: ProfessionalFormValues,
    professionalId: string
) {
    const { showToast } = useToast();

    const [form, setForm] = useState<ProfessionalFormValues>(initialData);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    //  Synchroniser le formulaire lorsque initialData change (nouveau professionnel)
    useEffect(() => {
        setForm(initialData);
        setAvatarFile(null);
    }, [initialData]);

    const updateField = <K extends keyof ProfessionalFormValues>(
        field: K,
        value: ProfessionalFormValues[K]
    ): void => {
        setForm(prev => ({
            ...prev,
            [field]: value,
        }));
    };

    const updateAddress = (
        field: keyof ProfessionalFormValues['address'],
        value: string
    ): void => {
        setForm(prev => ({
            ...prev,
            address: {
                ...prev.address,
                [field]: value,
            },
        }));
    };

    const updateAvatar = (
        value: string,
        file?: File | null
    ): void => {
        updateField('avatarUrl', value);

        if (file !== undefined) {
            setAvatarFile(file);
        }
    };

    const submit = async (): Promise<boolean> => {
        if (!form.fullName.trim() || !form.email.trim()) {
            showToast({
                type: 'error',
                message: 'Veuillez remplir les champs obligatoires.',
            });

            return false;
        }

        setIsSubmitting(true);
        setError(null);

        try {
            await updateProfessional(
                professionalId,
                form,
                avatarFile
            );

            showToast({
                type: 'success',
                message: 'Professionnel mis à jour avec succès.',
            });

            return true;
        } catch (err) {
            const message =
                err instanceof Error
                    ? err.message
                    : 'Erreur lors de la mise à jour.';

            setError(message);

            showToast({
                type: 'error',
                message,
            });

            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        form,
        updateField,
        updateAddress,
        updateAvatar,
        submit,
        isSubmitting,
        error,
    };
}
