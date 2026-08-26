import { useState } from 'react';
import { updateDepartment } from '../services/departmentService';
import { DepartmentFormValues, Department } from '../types/department';

export function useUpdateDepartment(department: Department) {
    const [form, setForm] = useState<DepartmentFormValues>({
        facilityId: department.facilityId,
        name: department.name,
        specialty: department.specialty,
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
            await updateDepartment(department.id, form);
        } catch (err) {
            setError('Erreur lors de la mise à jour.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { form, updateField, submit, isSubmitting, error };
}
