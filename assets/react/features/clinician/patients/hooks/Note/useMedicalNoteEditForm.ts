// useMedicalNoteEditForm.ts
import { useEffect, useState, useCallback } from 'react';
import { updateMedicalNote } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { PatientDossierData, PatientMedicalNote } from '../../types';

interface UseMedicalNoteEditFormProps {
    data: PatientDossierData;
    note: PatientMedicalNote;
    onSuccess: () => void;
    onClose: () => void;
}

export interface MedicalNoteEditFormState {
    content: string;
    notedAt: string;
}

export function useMedicalNoteEditForm({
                                           data,
                                           note,
                                           onSuccess,
                                           onClose,
                                       }: UseMedicalNoteEditFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [form, setForm] = useState<MedicalNoteEditFormState>({
        content: '',
        notedAt: '',
    });

    // Initialisation avec les données de la note
    useEffect(() => {
        setForm({
            content: note.content,
            notedAt: toDatetimeLocalValue(note.notedAt ?? note.createdAt),
        });
    }, [note]);

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
                await updateMedicalNote(note.id, {
                    medicalRecordId,
                    authorId,
                    content: form.content,
                    notedAt: new Date(form.notedAt).toISOString(),
                });
                showToast({
                    type: 'success',
                    message: 'Note médicale mise à jour avec succès.',
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
        [note.id, data, form, onSuccess, onClose, showToast]
    );

    return {
        form,
        isLoading,
        handleChange,
        handleSubmit,
    };
}

function toDatetimeLocalValue(date: string | Date): string {
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
