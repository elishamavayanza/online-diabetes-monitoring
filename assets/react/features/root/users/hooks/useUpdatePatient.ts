import { useState } from 'react';
import { PatientFormValues } from '../types/userForm.types';
import { updateUser } from "@/react/features/root/users/services/usersService";

export function useUpdatePatient(initialData: PatientFormValues) {
    const [form, setForm] = useState<PatientFormValues>(initialData);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = (field: keyof PatientFormValues, value: any) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof PatientFormValues['address'], value: string) => {
        setForm((prev) => ({ ...prev, address: { ...prev.address, [field]: value } }));
    };

    const updateAvatar = (url: string, file: File | null) => {
        setForm((prev) => ({ ...prev, avatarUrl: url, avatarFile: file }));
    };

    const submit = async (userId: string) => {
        setIsSubmitting(true);
        setError(null);
        try {
            await updateUser('patient', userId, form);
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
