import { useEffect, useState, useCallback } from 'react';
import { updateAppointment } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { PatientDossierData } from "@/react/features/clinician/patients/types";
import { PatientAppointment } from '../../types';
import { APPOINTMENT_MOTIFS } from '../../constants/appointmentMotifs';

interface UseAppointmentEditFormProps {
    data: PatientDossierData;
    appointment: PatientAppointment;
    onSuccess: () => void;
    onClose: () => void;
}

export interface AppointmentEditFormState {
    scheduledAt: string;
    durationMinutes: string;
    status: string;
    reason: string;
    notes: string;
    selectedMotifs: string[];
}

export function useAppointmentEditForm({
                                           data,
                                           appointment,
                                           onSuccess,
                                           onClose,
                                       }: UseAppointmentEditFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [form, setForm] = useState<AppointmentEditFormState>({
        scheduledAt: '',
        durationMinutes: '30',
        status: 'SCHEDULED',
        reason: '',
        notes: '',
        selectedMotifs: [],
    });

    // Initialisation avec les données du rendez-vous
    useEffect(() => {
        setForm({
            scheduledAt: toDatetimeLocalValue(appointment.scheduledAt),
            durationMinutes: String(appointment.durationMinutes ?? 30),
            status: appointment.status ?? 'SCHEDULED',
            reason: appointment.reason ?? '',
            notes: appointment.notes ?? '',
            selectedMotifs: extractMotifsFromReason(appointment.reason ?? ''),
        });
    }, [appointment]);

    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
            const { name, value } = e.target;
            setForm((prev) => ({ ...prev, [name]: value }));
        },
        []
    );

    const toggleMotif = useCallback((motif: string) => {
        setForm((prev) => {
            const isSelected = prev.selectedMotifs.includes(motif);
            return {
                ...prev,
                selectedMotifs: isSelected
                    ? prev.selectedMotifs.filter((m) => m !== motif)
                    : [...prev.selectedMotifs, motif],
            };
        });
    }, []);

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

            // Combiner motif texte et motifs cochés
            const motifs = form.selectedMotifs.join(', ');
            const finalReason = [form.reason.trim(), motifs].filter(Boolean).join(' - ');

            setIsLoading(true);
            try {
                await updateAppointment(appointment.id, {
                    patientId: data.profile.id,
                    professionalId,
                    organizationId,
                    scheduledAt: new Date(form.scheduledAt).toISOString(),
                    durationMinutes: Number(form.durationMinutes),
                    status: form.status,
                    reason: finalReason || undefined,
                    notes: form.notes || undefined,
                });
                showToast({
                    type: 'success',
                    message: 'Rendez-vous mis à jour avec succès.',
                });
                onSuccess();
                onClose();
            } catch (err) {
                showToast({
                    type: 'error',
                    message: err instanceof Error ? err.message : 'Erreur lors de la mise à jour.',
                });
            } finally {
                setIsLoading(false);
            }
        },
        [appointment.id, data, form, onSuccess, onClose, showToast]
    );

    return {
        form,
        isLoading,
        handleChange,
        toggleMotif,
        handleSubmit,
    };
}

function toDatetimeLocalValue(date: string | Date): string {
    const d = new Date(date);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

// Extrait les motifs connus d'une raison existante
function extractMotifsFromReason(reason: string): string[] {
    if (!reason) return [];
    return APPOINTMENT_MOTIFS.filter((motif) => reason.includes(motif));
}
