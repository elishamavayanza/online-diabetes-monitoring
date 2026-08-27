import { useState } from 'react';
import { PatientFormValues } from '../types/userForm.types';
import { createUser } from "@/react/features/root/users/services/usersService";

const initialPatient: PatientFormValues = {
    email: '',
    password: '',
    fullName: '',
    phone: '',
    gender: 'MALE',
    locale: 'fr',
    dateOfBirth: '',
    placeOfBirth: '',
    bloodType: '',
    heightCm: '',
    avatarUrl: '',
    avatarFile: null,
    address: { street: '', city: '', postalCode: '', country: 'RDC' },
};

export function useCreatePatient() {
    const [form, setForm] = useState<PatientFormValues>(initialPatient);
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

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await createUser('patient', form);
            setForm(initialPatient);
        } catch (err) {
            setError('Erreur lors de la création.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error };
}
