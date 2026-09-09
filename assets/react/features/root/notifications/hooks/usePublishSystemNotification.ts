import { useState } from 'react';
import { publishSystemNotification } from '../services/notificationsService';
import { CreateSystemNotificationPayload } from '../types';

const initialForm: CreateSystemNotificationPayload = {
    scope: 'GLOBAL',
    type: 'SYSTEM_ALERT',
    title: '',
    body: '',
    channel: 'IN_APP',
};

export function usePublishSystemNotification() {
    const [form, setForm] = useState<CreateSystemNotificationPayload>(initialForm);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof CreateSystemNotificationPayload>(
        field: K,
        value: CreateSystemNotificationPayload[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async (): Promise<boolean> => {
        setIsSubmitting(true);
        setError(null);
        try {
            await publishSystemNotification(form);
            setForm(initialForm);
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la publication.';
            setError(message);
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error };
}