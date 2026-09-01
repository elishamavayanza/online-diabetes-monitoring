import { useEffect, useState } from 'react';
import { createDiagnosis, updateDiagnosis } from '../../services/dossierActionsService';
import { PatientDiagnosis, PatientDossierData } from '../../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { useAuth } from '@/react/app/providers/AuthProvider';

interface UseDiagnosisFormProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    diagnosis?: PatientDiagnosis;
    onSuccess: () => void;
}

const INITIAL_FORM = {
    conditionName: '',
    description: '',
    diagnosedAt: '',
    status: 'CONFIRMED',
};

export function useDiagnosisForm({
                                     isOpen,
                                     onClose,
                                     data,
                                     diagnosis,
                                     onSuccess,
                                 }: UseDiagnosisFormProps) {
    const { showToast } = useToast();
    const { user } = useAuth();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState(INITIAL_FORM);

    //  on récupère l'ID directement depuis le contexte d'authentification
    const doctorId = user?.id && user.id !== 'unknown' ? user.id : null;
    const isEdit = !!diagnosis;

    useEffect(() => {
        if (isOpen) {
            setForm({
                conditionName: diagnosis?.conditionName ?? '',
                description: diagnosis?.description ?? '',
                diagnosedAt: diagnosis?.diagnosedAt
                    ? new Date(diagnosis.diagnosedAt).toISOString().slice(0, 16)
                    : new Date().toISOString().slice(0, 16),
                status: diagnosis?.status ?? 'CONFIRMED',
            });
            setError(null);
        }
    }, [isOpen, diagnosis]);

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!doctorId) {
            setError("Impossible d'identifier le médecin.");
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            const payload = {
                patientId: data.profile.id,
                doctorId,
                conditionName: form.conditionName,
                description: form.description || undefined,
                diagnosedAt: new Date(form.diagnosedAt).toISOString(),
                status: form.status,
                medicalRecordId: data.record?.id,
            };
            if (isEdit && diagnosis) {
                await updateDiagnosis(diagnosis.id, payload);
                showToast({ type: 'success', message: 'Diagnostic mis à jour avec succès.' });
            } else {
                await createDiagnosis(payload);
                showToast({ type: 'success', message: 'Diagnostic ajouté avec succès.' });
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
