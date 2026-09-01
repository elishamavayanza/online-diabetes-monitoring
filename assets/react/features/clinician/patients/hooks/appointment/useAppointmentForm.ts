// useAppointmentForm.ts
import { useEffect, useState, useCallback } from 'react';
import { createAppointment } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseAppointmentFormProps {
    data: PatientDossierData;
    defaultDate?: Date | null;
    defaultReason?: string;
    onSuccess: () => void;
    onClose: () => void;
}

export interface AppointmentFormState {
    scheduledAt: string;
    durationMinutes: string;
    status: string;
    reason: string;
    notes: string;
}

export function useAppointmentForm({
                                       data,
                                       defaultDate,
                                       defaultReason = '',
                                       onSuccess,
                                       onClose,
                                   }: UseAppointmentFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [form, setForm] = useState<AppointmentFormState>({
        scheduledAt: '',
        durationMinutes: '30',
        status: 'SCHEDULED',
        reason: defaultReason,
        notes: '',
    });

    // Initialisation à l'ouverture
    useEffect(() => {
        setForm({
            scheduledAt: defaultDate ? toDatetimeLocalValue(defaultDate) : '',
            durationMinutes: '30',
            status: defaultReason ? 'CONFIRMED' : 'SCHEDULED',
            reason: defaultReason,
            notes: '',
        });
    }, [defaultDate, defaultReason]);

    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
            const { name, value } = e.target;
            setForm((prev) => ({ ...prev, [name]: value }));
        },
        []
    );

    const handleSubmit = useCallback(
        async (e: React.FormEvent) => {
            e.preventDefault();
            const professionalId = getCurrentUserIdFromToken();
            const organizationId = data.profile.organizationId;

            if (!professionalId || !organizationId) {
                showToast({
                    type: 'error',
                    message: "Impossible d'identifier le professionnel ou l'organisation.",
                });
                return;
            }

            setIsLoading(true);
            try {
                await createAppointment({
                    patientId: data.profile.id,
                    professionalId,
                    organizationId,
                    scheduledAt: new Date(form.scheduledAt).toISOString(),
                    durationMinutes: Number(form.durationMinutes),
                    status: form.status,
                    reason: form.reason || undefined,
                    notes: form.notes || undefined,
                });
                showToast({
                    type: 'success',
                    message: 'Rendez-vous créé avec succès.',
                });
                onSuccess();
                onClose();
            } catch (err) {
                showToast({
                    type: 'error',
                    message: err instanceof Error ? err.message : 'Erreur lors de la création.',
                });
            } finally {
                setIsLoading(false);
            }
        },
        [data, form, onSuccess, onClose, showToast]
    );

    return {
        form,
        isLoading,
        handleChange,
        handleSubmit,
    };
}

// Fonction utilitaire (exportée si besoin ailleurs)
export function toDatetimeLocalValue(date?: Date | null): string {
    if (!date) return '';
    const d = new Date(date);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
