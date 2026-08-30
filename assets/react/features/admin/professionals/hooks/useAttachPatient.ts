import { useEffect, useState } from 'react';
import { assignPatientToProfessional, fetchPatientsForAssignment } from '../services/careTeamService';
import { CareTeamAssignmentFormValues } from '../types/types';

export function useAttachPatient(professionalId: string) {
    const [patients, setPatients] = useState<{ id: string; nom: string }[]>([]);
    const [form, setForm] = useState<CareTeamAssignmentFormValues>({
        patientId: '',
        professionalId,
        role: 'PRIMARY_CLINICIAN',
        startDate: '',
        endDate: '',
        active: true,
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadPatients = async () => {
            try {
                const data = await fetchPatientsForAssignment();
                setPatients(data);
            } catch (err) {
                setError('Impossible de charger les patients.');
            }
        };
        loadPatients();
    }, []);

    const updateField = <K extends keyof CareTeamAssignmentFormValues>(
        field: K,
        value: CareTeamAssignmentFormValues[K]
    ) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await assignPatientToProfessional(form);
            // Réinitialiser après succès
            setForm({
                patientId: '',
                professionalId,
                role: 'PRIMARY_CLINICIAN',
                startDate: '',
                endDate: '',
                active: true,
            });
        } catch (err) {
            setError('Erreur lors de l’affectation.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return { patients, form, updateField, submit, isSubmitting, error };
}
