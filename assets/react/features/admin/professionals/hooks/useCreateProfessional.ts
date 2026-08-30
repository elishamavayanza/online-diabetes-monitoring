import { useState } from 'react';
import { createProfessional } from '../services/professionalsService';
// import { ProfessionalFormValues } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {ProfessionalFormValues} from "@/react/features/admin/professionals/types/types";

const initialForm: ProfessionalFormValues = {
    email: '',
    password: '',
    fullName: '',
    phone: '',
    gender: 'UNSPECIFIED',
    locale: 'fr',
    avatarUrl: '',
    avatarFile: null,
    address: { street: '', city: '', postalCode: '', country: 'RDC' },
    licenseNumber: '',
    professionalType: 'CLINICIAN',
    specialty: '',
    signatureUrl: '',
};

export function useCreateProfessional() {
    const { showToast } = useToast();
    const [form, setForm] = useState<ProfessionalFormValues>(initialForm);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = <K extends keyof ProfessionalFormValues>(field: K, value: ProfessionalFormValues[K]) => {
        setForm((prev: ProfessionalFormValues) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof ProfessionalFormValues['address'], value: string) => {
        setForm((prev: ProfessionalFormValues) => ({
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
            showToast({ type: 'error', message: 'Veuillez remplir tous les champs obligatoires.' });
            return false;
        }
        setIsSubmitting(true);
        setError(null);
        try {
            await createProfessional(form, avatarFile);
            showToast({ type: 'success', message: 'Professionnel créé avec succès.' });
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
