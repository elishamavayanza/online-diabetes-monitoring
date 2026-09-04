// hooks/useRequestAppointmentModal.ts
import { useEffect, useState } from 'react';
import {
    fetchPatientTeam,
    createAppointmentRequest,
} from '../services/patientAppointmentsService';
import { ProfessionalOption } from '../types';
import {
    getCurrentUserIdFromToken,
    getCurrentUserOrganizationId,
} from '@/react/utils/authUtils';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseRequestAppointmentModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

export function useRequestAppointmentModal({
                                               isOpen,
                                               onClose,
                                               onSuccess,
                                           }: UseRequestAppointmentModalProps) {
    const { showToast } = useToast();
    const [professionals, setProfessionals] = useState<ProfessionalOption[]>([]);
    const [professionalId, setProfessionalId] = useState('');
    const [date, setDate] = useState('');
    const [time, setTime] = useState('');
    const [duration, setDuration] = useState('30');
    const [reason, setReason] = useState('');
    const [notes, setNotes] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Charger l'équipe de soins lorsque le modal s'ouvre
    useEffect(() => {
        if (isOpen) {
            const patientId = getCurrentUserIdFromToken();
            if (patientId) {
                fetchPatientTeam(patientId)
                    .then(setProfessionals)
                    .catch(() =>
                        showToast({
                            type: 'error',
                            message: 'Erreur lors du chargement de votre équipe.',
                        })
                    );
            }
        }
    }, [isOpen, showToast]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const patientId = getCurrentUserIdFromToken();
        const organizationId = getCurrentUserOrganizationId();

        if (!patientId || !organizationId || !professionalId || !date || !time) {
            showToast({
                type: 'error',
                message: 'Veuillez remplir tous les champs obligatoires.',
            });
            return;
        }

        const scheduledAt = new Date(`${date}T${time}:00`).toISOString();
        setIsSubmitting(true);
        try {
            await createAppointmentRequest({
                patientId,
                professionalId,
                organizationId,
                scheduledAt,
                durationMinutes: parseInt(duration, 10),
                reason: reason.trim() || undefined,
                notes: notes.trim() || undefined,
            });
            showToast({
                type: 'success',
                message: 'Demande de rendez-vous envoyée.',
            });
            onSuccess();
            onClose();
        } catch (err) {
            showToast({
                type: 'error',
                message: "Erreur lors de la demande de rendez-vous.",
            });
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        professionals,
        professionalId,
        setProfessionalId,
        date,
        setDate,
        time,
        setTime,
        duration,
        setDuration,
        reason,
        setReason,
        notes,
        setNotes,
        isSubmitting,
        handleSubmit,
    };
}
