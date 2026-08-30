import { useState } from 'react';
import { createPatient } from '../services/patientsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {PatientFormValues} from "@/react/features/admin/patients/types/types";

const initialForm: PatientFormValues = {
    email: '',
    password: '',
    fullName: '',
    phone: '',
    gender: 'UNSPECIFIED',
    locale: 'fr',
    avatarUrl: '',
    avatarFile: null,
    address: { street: '', city: '', postalCode: '', country: 'RDC' },
    dateOfBirth: '',
    placeOfBirth: '',
    bloodType: '',
    heightCm: '',
};

export function useCreatePatient() {
    const { showToast } = useToast();
    const [form, setForm] = useState<PatientFormValues>(initialForm);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

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
        if (!form.fullName.trim() || !form.email.trim() || !form.password.trim()) {
            showToast({ type: 'error', message: 'Veuillez remplir les champs obligatoires.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            await createPatient(form, avatarFile);
            showToast({ type: 'success', message: 'Patient créé avec succès.' });
            setForm(initialForm);
            setAvatarFile(null);
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
