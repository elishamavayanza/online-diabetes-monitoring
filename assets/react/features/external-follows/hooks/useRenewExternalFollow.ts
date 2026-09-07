import { useState } from 'react';
import { renewExternalFollow } from '../services/externalFollowsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface Options {
    onSuccess?: () => void;
}

export function useRenewExternalFollow(organizationId: string, options?: Options) {
    const { showToast } = useToast();
    const [durationDays, setDurationDays] = useState(90);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const submit = async (invitationId: string): Promise<boolean> => {
        if (durationDays < 1 || durationDays > 730) {
            showToast({ type: 'error', message: 'La durée doit être comprise entre 1 et 730 jours.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await renewExternalFollow(organizationId, invitationId, { durationDays });
            showToast({ type: 'success', message: 'Délai prolongé avec succès.' });
            options?.onSuccess?.();
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors du renouvellement.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { durationDays, setDurationDays, submit, isSubmitting, error };
}