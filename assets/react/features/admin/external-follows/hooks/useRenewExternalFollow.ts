import { useState } from 'react';
import { renewExternalFollow } from '../services/externalFollowsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface Options {
    onSuccess?: () => void;
}

export function useRenewExternalFollow(organizationId: string, options?: Options) {
    const { showToast } = useToast();
    const [durationDays, setDurationDays] = useState<number | null>(90);
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const submit = async (invitationId: string): Promise<boolean> => {
        const hasDateRange = Boolean(startDate && endDate);
        if (hasDateRange) {
            if (endDate <= startDate) {
                showToast({ type: 'error', message: 'La date de fin doit être postérieure à la date de début.' });
                return false;
            }
        } else if (durationDays === null || durationDays < 1 || durationDays > 730) {
            showToast({ type: 'error', message: 'La durée doit être comprise entre 1 et 730 jours.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await renewExternalFollow(organizationId, invitationId, {
                durationDays: hasDateRange ? null : durationDays,
                startDate: hasDateRange ? startDate : null,
                endDate: hasDateRange ? endDate : null,
            });
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

    return { durationDays, setDurationDays, startDate, setStartDate, endDate, setEndDate, submit, isSubmitting, error };
}