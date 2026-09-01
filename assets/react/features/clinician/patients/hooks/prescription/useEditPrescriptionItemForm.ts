import { useEffect, useState } from 'react';
import { fetchMedications } from '@/react/features/admin/medications/services/medicationsService';
import { PrescriptionItem } from '../../types'; //  type correct
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {updatePrescriptionItem} from "@/react/features/clinician/patients/services/medicalRecordService";

interface UseEditPrescriptionItemFormProps {
    isOpen: boolean;
    onClose: () => void;
    item: PrescriptionItem | null;
    onSuccess: () => void;
}

const INITIAL_FORM = {
    medicationId: '',
    dosage: '',
    quantity: '1.00',
    morning: true,
    noon: false,
    evening: true,
    instructions: '',
};

export function useEditPrescriptionItemForm({
                                                isOpen,
                                                onClose,
                                                item,
                                                onSuccess,
                                            }: UseEditPrescriptionItemFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [medications, setMedications] = useState<{ value: string; label: string }[]>([]);
    const [form, setForm] = useState(INITIAL_FORM);

    useEffect(() => {
        if (isOpen && item) {
            setForm({
                medicationId: item.medicationId,
                dosage: item.dosage,
                quantity: item.quantity,
                morning: item.morning,
                noon: item.noon,
                evening: item.evening,
                instructions: item.instructions ?? '',
            });
            setError(null);
        }
    }, [isOpen, item]);

    useEffect(() => {
        if (isOpen) {
            fetchMedications()
                .then((list) => setMedications(list.map((m) => ({ value: m.id, label: m.name }))))
                .catch(() => {
                    setMedications([]);
                    showToast({ type: 'error', message: 'Impossible de charger les médicaments.' });
                });
        }
    }, [isOpen, showToast]);

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        const { name, value, type } = e.target;
        if (type === 'checkbox') {
            const checked = (e.target as HTMLInputElement).checked;
            setForm((prev) => ({ ...prev, [name]: checked }));
        } else {
            setForm((prev) => ({ ...prev, [name]: value }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!item) return;

        setIsLoading(true);
        setError(null);
        try {
            await updatePrescriptionItem(item.id, {
                prescriptionId: item.prescriptionId, // ✅ champ requis
                medicationId: form.medicationId,
                dosage: form.dosage,
                quantity: form.quantity,
                morning: form.morning,
                noon: form.noon,
                evening: form.evening,
                instructions: form.instructions || undefined,
            });
            showToast({ type: 'success', message: 'Médicament modifié avec succès.' });
            onSuccess();
            onClose();
        } catch (err) {
            const message = err instanceof Error ? err.message : "Erreur lors de la modification.";
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    };

    return {
        form,
        medications,
        isLoading,
        error,
        handleChange,
        handleSubmit,
    };
}
