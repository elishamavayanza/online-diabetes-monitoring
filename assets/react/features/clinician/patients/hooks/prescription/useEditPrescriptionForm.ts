import { useEffect, useState } from 'react';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData, PatientPrescription } from '../../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {updatePrescription} from "@/react/features/clinician/patients/services/medicalRecordService";
// import { updatePrescription } from '../../services/dossierActionsService'; // ✅ bon import

interface UseEditPrescriptionFormProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    prescription: PatientPrescription | null;
    onSuccess: () => void;
}

type PrescriptionStatus = 'ACTIVE' | 'DRAFT';

interface PrescriptionFormState {
    startDate: string;
    endDate: string;
    status: PrescriptionStatus;
    notes: string;
}

export function useEditPrescriptionForm({
                                            isOpen,
                                            onClose,
                                            data,
                                            prescription,
                                            onSuccess,
                                        }: UseEditPrescriptionFormProps) {
    const { showToast } = useToast();
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState<PrescriptionFormState>({
        startDate: '',
        endDate: '',
        status: 'ACTIVE',
        notes: '',
    });

    useEffect(() => {
        if (isOpen && prescription) {
            setForm({
                startDate: prescription.startDate
                    ? new Date(prescription.startDate).toISOString().slice(0, 16)
                    : '',
                endDate: prescription.endDate
                    ? new Date(prescription.endDate).toISOString().slice(0, 16)
                    : '',
                //  convertir et valider le statut
                status: prescription.status === 'DRAFT' ? 'DRAFT' : 'ACTIVE',
                notes: prescription.notes ?? '',
            });
            setError(null);
        }
    }, [isOpen, prescription]);

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
    ) => {
        const { name, value } = e.target;
        if (name === 'status') {
            //  s'assurer que value est un PrescriptionStatus valide
            const status = value === 'DRAFT' ? 'DRAFT' : 'ACTIVE';
            setForm((prev) => ({ ...prev, status }));
        } else {
            setForm((prev) => ({ ...prev, [name]: value }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!prescription) return;

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
            await updatePrescription(prescription.id, {
                patientId: data.profile.id,
                prescriberId,
                organizationId,
                startDate: new Date(form.startDate).toISOString(),
                endDate: form.endDate ? new Date(form.endDate).toISOString() : undefined,
                status: form.status, //  maintenant compatible
                notes: form.notes || undefined,
            });
            showToast({ type: 'success', message: 'Prescription mise à jour avec succès.' });
            onSuccess();
            onClose();
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la mise à jour.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    };

    return { form, handleChange, handleSubmit, isLoading, error };
}
