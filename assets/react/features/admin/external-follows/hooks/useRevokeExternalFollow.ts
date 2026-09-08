import { useState } from 'react';
import { revokeExternalFollow } from '../services/externalFollowsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface Options {
    onSuccess?: () => void;
}

export function useRevokeExternalFollow(organizationId: string, options?: Options) {
    const { showToast } = useToast();
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const submit = async (invitationId: string): Promise<boolean> => {
        setIsSubmitting(true);
        setError(null);
        try {
            await revokeExternalFollow(organizationId, invitationId);
            showToast({ type: 'success', message: 'Accès coupé avec succès.' });
            options?.onSuccess?.();
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la coupure de l’accès.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { submit, isSubmitting, error };
}