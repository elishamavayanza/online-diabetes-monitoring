import { useState } from 'react';
import { ProfessionalFormValues } from '../types/userForm.types';
import { updateUser } from "@/react/features/root/users/services/usersService";


const defaultProfessional: ProfessionalFormValues = {
    email: '',
    password: '',
    fullName: '',
    phone: '',
    gender: 'MALE',
    locale: 'fr',
    licenseNumber: '',
    professionalType: 'CLINICIAN',
    specialty: '',
    signatureUrl: '',
    avatarUrl: '',
    avatarFile: null,
    address: { street: '', city: '', postalCode: '', country: 'RDC' },
};

export function useUpdateProfessional(initialData: ProfessionalFormValues) {
    const [form, setForm] = useState<ProfessionalFormValues>(initialData || defaultProfessional);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = (field: keyof ProfessionalFormValues, value: any) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const updateAddress = (field: keyof ProfessionalFormValues['address'], value: string) => {
        setForm((prev) => ({ ...prev, address: { ...prev.address, [field]: value } }));
    };

    const updateAvatar = (url: string, file: File | null) => {
        setForm((prev) => ({ ...prev, avatarUrl: url, avatarFile: file }));
    };

    const submit = async (userId: string) => {
        setIsSubmitting(true);
        setError(null);
        try {
            await updateUser('professional', userId, form);
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
