import { useState } from 'react';
import { createExternalFollow } from '../services/externalFollowsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export interface CreateInvitationFormValues {
    patientId: string;
    email: string;
    durationDays: number;
    message: string;
}

interface Options {
    onSuccess?: () => void;
}

export function useCreateExternalFollow(organizationId: string, options?: Options) {
    const { showToast } = useToast();
    const [form, setForm] = useState<CreateInvitationFormValues>({
        patientId: '',
        email: '',
        durationDays: 90,
        message: '',
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof CreateInvitationFormValues>(field: K, value: CreateInvitationFormValues[K]) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const reset = () => {
        setForm({ patientId: '', email: '', durationDays: 90, message: '' });
        setError(null);
    };

    const submit = async (): Promise<boolean> => {
        if (!form.patientId) {
            showToast({ type: 'error', message: 'Veuillez sélectionner le patient à suivre.' });
            return false;
        }
        if (!form.email.trim()) {
            showToast({ type: 'error', message: 'L’email du professionnel invité est obligatoire.' });
            return false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())) {
            showToast({ type: 'error', message: 'L’email saisi n’est pas valide.' });
            return false;
        }
        if (form.durationDays < 1 || form.durationDays > 730) {
            showToast({ type: 'error', message: 'La durée doit être comprise entre 1 et 730 jours.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await createExternalFollow(organizationId, {
                patientId: Number(form.patientId),
                email: form.email.trim(),
                durationDays: form.durationDays,
                message: form.message.trim() || null,
            });
            showToast({ type: 'success', message: 'Invitation envoyée avec succès.' });
            reset();
            options?.onSuccess?.();
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création de l’invitation.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error, reset };
}