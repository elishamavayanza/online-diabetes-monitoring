import { useEffect, useState } from 'react';
import { createPrescription } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UsePrescriptionFormProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    onSuccess: () => void;
}

const INITIAL_FORM = {
    startDate: '',
    endDate: '',
    status: 'ACTIVE',
    notes: '',
};

export function usePrescriptionForm({
                                        isOpen,
                                        onClose,
                                        data,
                                        onSuccess,
                                    }: UsePrescriptionFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState(INITIAL_FORM);

    useEffect(() => {
        if (isOpen) {
            const today = new Date().toISOString().slice(0, 16);
            setForm({ startDate: today, endDate: '', status: 'ACTIVE', notes: '' });
            setError(null);
        }
    }, [isOpen]);

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const prescriberId = getCurrentUserIdFromToken();
        const organizationId = data.profile.organizationId;

        if (!prescriberId || !organizationId) {
            setError("Impossible d'identifier le prescripteur ou l'organisation.");
            showToast({ type: 'error', message: "Prescripteur ou organisation introuvable." });
            return;
        }

        setIsLoading(true);
        setError(null);

        try {
            await createPrescription({
                patientId: data.profile.id,
                prescriberId,
                organizationId,
                startDate: new Date(form.startDate).toISOString(),
                endDate: form.endDate ? new Date(form.endDate).toISOString() : undefined,
                status: form.status,
                notes: form.notes || undefined,
            });
            showToast({ type: 'success', message: 'Prescription créée avec succès.' });
            onSuccess();
            onClose();
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
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
    };
}
