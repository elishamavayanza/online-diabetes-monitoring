import { useState } from 'react';
import { createDepartment } from '../services/departmentService';
import { DepartmentFormValues } from '../types/department';

const initialForm: DepartmentFormValues = {
    facilityId: '',
    name: '',
    specialty: '',
};

export function useCreateDepartment(facilityId: string) {
    const [form, setForm] = useState<DepartmentFormValues>({
        ...initialForm,
        facilityId,
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const updateField = (field: keyof DepartmentFormValues, value: string) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await createDepartment(form);
            setForm({ ...initialForm, facilityId });
        } catch (err) {
            setError('Erreur lors de la création.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error };
}
