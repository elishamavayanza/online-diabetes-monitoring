import { useState, useEffect } from 'react';
import { updatePatient } from '../services/patientsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { PatientFormValues } from "@/react/features/admin/patients/types/types";

export function useUpdatePatient(initialData: PatientFormValues, patientId: string) {
    const { showToast } = useToast();
    const [form, setForm] = useState<PatientFormValues>(initialData);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setForm(initialData);
        setAvatarFile(null);
    }, [initialData]);

    const updateField = <K extends keyof PatientFormValues>(
        field: K,
        value: PatientFormValues[K]
    ) => {
        setForm((prev: PatientFormValues) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof PatientFormValues['address'], value: string) => {
        setForm((prev: PatientFormValues) => ({
            ...prev,
            address: { ...prev.address, [field]: value },
        }));
    };

    const updateAvatar = (value: string, file?: File | null) => {
        updateField('avatarUrl', value);
        if (file) setAvatarFile(file);
    };

    const submit = async (): Promise<boolean> => {
        if (!form.fullName.trim()) {
            showToast({ type: 'error', message: 'Le nom complet est obligatoire.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await updatePatient(patientId, form, avatarFile);
            showToast({ type: 'success', message: 'Patient mis à jour avec succès.' });
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la mise à jour.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
