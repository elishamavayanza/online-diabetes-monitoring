import { useEffect, useState } from 'react';
import { createAllergy, updateAllergy } from '../services/dossierActionsService';
import { PatientAllergy, PatientDossierData } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseAllergyFormProps {
    isOpen: boolean;
    data: PatientDossierData;
    allergy?: PatientAllergy;
    onSuccess: () => void;
    onClose: () => void;
}

const INITIAL_FORM = {
    name: '',
    severity: 'MODERATE',
    reaction: '',
    notes: '',
    diagnosedAt: '',
};

export function useAllergyForm({ isOpen, data, allergy, onSuccess, onClose }: UseAllergyFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState(INITIAL_FORM);

    const isEdit = !!allergy;

    useEffect(() => {
        if (isOpen) {
            setForm({
                name: allergy?.name ?? '',
                severity: allergy?.severity ?? 'MODERATE',
                reaction: allergy?.reaction ?? '',
                notes: allergy?.notes ?? '',
                diagnosedAt: allergy
                    ? new Date().toISOString().slice(0, 16)
                    : new Date().toISOString().slice(0, 16),
            });
            setError(null);
        }
    }, [isOpen, allergy]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                name: form.name,
                severity: form.severity,
                reaction: form.reaction || undefined,
                notes: form.notes || undefined,
                diagnosedAt: new Date(form.diagnosedAt).toISOString(),
            };
            if (isEdit && allergy) {
                await updateAllergy(allergy.id, payload);
                showToast({ type: 'success', message: 'Allergie mise à jour avec succès.' });
            } else {
                await createAllergy(payload);
                showToast({ type: 'success', message: 'Allergie ajoutée avec succès.' });
            }
            onSuccess();
            onClose();
        } catch (err) {
            const message = err instanceof Error ? err.message : "Erreur lors de l'enregistrement.";
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    };

    return {
        form,
        handleChange,
        handleSubmit,
        isLoading,
        error,
        isEdit,
    };
}
