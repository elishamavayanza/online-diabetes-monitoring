import { useState, useEffect } from 'react';
import {
    AttachPeopleFormValues,
    CareTeamAssignmentItem,
    ProfessionalOption,
} from '../types/attachPeople.types';
import {
    attachProfessionalsToPatient,
    fetchProfessionalsForAttach,
    fetchExistingAssignments,
    updateAssignmentsForPatient,
} from '../services/attachPeopleService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

function generateId(): string {
    return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
}

export function useAttachPeople(patientId: string, mode: 'create' | 'edit' = 'create') {
    const { showToast } = useToast();
    const [professionals, setProfessionals] = useState<ProfessionalOption[]>([]);
    const [assignments, setAssignments] = useState<CareTeamAssignmentItem[]>([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const loadProfessionals = async () => {
            try {
                const data = await fetchProfessionalsForAttach();
                setProfessionals(data);
            } catch (err) {
                const message = 'Impossible de charger les professionnels.';
                setError(message);
                showToast({ type: 'error', message });
            }
        };
        loadProfessionals();
    }, [showToast]);

    useEffect(() => {
        if (mode === 'edit') {
            const loadExisting = async () => {
                try {
                    const existing = await fetchExistingAssignments(patientId);
                    setAssignments(existing);
                } catch (err) {
                    const message = 'Impossible de charger les affectations existantes.';
                    setError(message);
                    showToast({ type: 'error', message });
                }
            };
            loadExisting();
        }
    }, [mode, patientId, showToast]);

    const addAssignment = () => {
        const newAssignment: CareTeamAssignmentItem = {
            id: generateId(),
            professionalId: '',
            role: 'PRIMARY_CLINICIAN',
            startDate: '',
            endDate: '',
            active: true,
        };
        setAssignments((prev) => [...prev, newAssignment]);
    };

    const removeAssignment = (id: string) => {
        setAssignments((prev) => prev.filter((a) => a.id !== id));
    };

    const updateAssignment = (
        id: string,
        field: keyof CareTeamAssignmentItem,
        value: any
    ) => {
        setAssignments((prev) =>
            prev.map((a) => (a.id === id ? { ...a, [field]: value } : a))
        );
    };

    const submit = async (): Promise<boolean> => {
        // Validation simple
        for (const assignment of assignments) {
            if (!assignment.professionalId || !assignment.startDate) {
                showToast({ type: 'error', message: 'Chaque affectation doit avoir un professionnel et une date de début.' });
                return false;
            }
        }

        setIsSubmitting(true);
        setError(null);
        try {
            const payload: AttachPeopleFormValues = {
                patientId,
                assignments,
            };
            if (mode === 'edit') {
                await updateAssignmentsForPatient(patientId, payload);
                showToast({ type: 'success', message: 'Affectations mises à jour avec succès.' });
            } else {
                await attachProfessionalsToPatient(payload);
                showToast({ type: 'success', message: 'Professionnels attachés avec succès.' });
            }
            setAssignments([]);
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : "Erreur lors de l'enregistrement.";
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        professionals,
        assignments,
        addAssignment,
        removeAssignment,
        updateAssignment,
        submit,
        isSubmitting,
        error,
    };
}
