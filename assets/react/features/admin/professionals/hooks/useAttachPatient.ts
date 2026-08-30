import { useState, useEffect } from 'react';
import { assignPatientToProfessional, fetchPatientsForAssignment } from '../services/careTeamService';
import { CareTeamAssignmentFormValues } from '../types/types';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import { formatDateToApi } from '@/utils/date.utils';

interface PatientOption {
    id: string;
    nom: string;
}

const initialForm: CareTeamAssignmentFormValues = {
    patientId: 0,
    professionalId: 0,
    role: 'PRIMARY_CLINICIAN',
    startDate: formatDateToApi(new Date()),
    endDate: '',
    active: true,
};

export function useAttachPatient(professionalId: string) {
    const { user } = useAuth();
    const { showToast } = useToast();
    const [patients, setPatients] = useState<PatientOption[]>([]);
    const [form, setForm] = useState<CareTeamAssignmentFormValues>({
        ...initialForm,
        professionalId: Number(professionalId),
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadPatients = async () => {
            try {
                const data = await fetchPatientsForAssignment();
                setPatients(data);
            } catch (err) {
                console.error('Erreur chargement patients:', err);
                setError('Impossible de charger les patients.');
            }
        };
        loadPatients();
    }, []);

    const updateField = <K extends keyof CareTeamAssignmentFormValues>(
        field: K,
        value: CareTeamAssignmentFormValues[K]
    ) => {
        setForm((prev: CareTeamAssignmentFormValues) => ({
            ...prev,
            [field]: value,
        }));
    };

    const getOrganizationId = (): string | null => {
        if (user?.organizationId) return user.organizationId;
        const token = tokenStorage.getAccessToken();
        if (token) {
            try {
                const payload = decodeJwtPayload(token);
                const orgs = payload?.organizations;
                if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
                    return String(orgs[0].organization_id);
                }
            } catch (e) {
                console.error('Erreur décodage token:', e);
            }
        }
        return null;
    };

    const submit = async (): Promise<boolean> => {
        if (!form.patientId || !form.startDate) {
            showToast({ type: 'error', message: 'Veuillez remplir les champs obligatoires.' });
            return false;
        }

        const organizationId = getOrganizationId();
        if (!organizationId) {
            showToast({ type: 'error', message: 'Organisation introuvable.' });
            return false;
        }

        setIsSubmitting(true);
        setError(null);
        try {
            // ✅ Conversion explicite en entiers
            const payloadToSend = {
                ...form,
                patientId: Number(form.patientId),
                professionalId: Number(form.professionalId),
            };

            await assignPatientToProfessional(organizationId, payloadToSend);
            showToast({ type: 'success', message: 'Patient affecté avec succès.' });
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : "Erreur lors de l'affectation.";
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return { patients, form, updateField, submit, isSubmitting, error };
}
