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

    const submit = async (): Promise<number | null> => {
        if (!form.fullName.trim() || !form.email.trim() || !form.password.trim() || !form.licenseNumber.trim()) {
            showToast({ type: 'error', message: 'Veuillez remplir tous les champs obligatoires, y compris le numéro de licence.' });
            return null;
        }
        setIsSubmitting(true);
        setError(null);
        try {
            const professionalId = await createProfessional(form, avatarFile);
            showToast({ type: 'success', message: 'Professionnel créé avec succès.' });
            return professionalId;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
            setError(message);
            showToast({ type: 'error', message });
            return null;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
