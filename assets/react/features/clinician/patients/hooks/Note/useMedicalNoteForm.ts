// useMedicalNoteForm.ts
import { useEffect, useState, useCallback } from 'react';
import { createMedicalNote } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseMedicalNoteFormProps {
    data: PatientDossierData;
    defaultDate?: Date | null;
    onSuccess: () => void;
    onClose: () => void;
}

export interface MedicalNoteFormState {
    content: string;
    notedAt: string;
}

export function useMedicalNoteForm({
                                       data,
                                       defaultDate,
                                       onSuccess,
                                       onClose,
                                   }: UseMedicalNoteFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [form, setForm] = useState<MedicalNoteFormState>({
        content: '',
        notedAt: '',
    });

    // Initialisation à l'ouverture
    useEffect(() => {
        setForm({
            content: '',
            notedAt: toDatetimeLocalValue(defaultDate),
        });
    }, [defaultDate]);

    const handleChange = useCallback(
        (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
            const { name, value } = e.target;
            setForm((prev) => ({ ...prev, [name]: value }));
        },
        []
    );

    const handleSubmit = useCallback(
        async (e: React.FormEvent) => {
            e.preventDefault();
            const authorId = getCurrentUserIdFromToken();
            const medicalRecordId = data.record?.id;

            if (!authorId || !medicalRecordId) {
                showToast({
                    type: 'error',
                    message: 'Dossier médical ou auteur introuvable.',
                });
                return;
            }

            setIsLoading(true);
            try {
                await createMedicalNote({
                    medicalRecordId,
                    authorId,
                    content: form.content,
                    notedAt: new Date(form.notedAt).toISOString(),
                });
                showToast({
                    type: 'success',
                    message: 'Note médicale enregistrée avec succès.',
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

// Fonction utilitaire locale
function toDatetimeLocalValue(date?: Date | null): string {
    const now = date ? new Date(date) : new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
}
