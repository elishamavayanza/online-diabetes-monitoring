import { useEffect, useState } from 'react';
import {
    AttachPeopleFormValues,
    CareTeamAssignmentItem,
    ProfessionalOption,
} from '../types/attachPeople.types';
import {
    attachProfessionalsToPatient,
    fetchProfessionalsForAttach,
    fetchExistingAssignments, updateAssignmentsForPatient,   // ✅ à créer
} from '../services/attachPeopleService';

function generateId(): string {
    return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
}

export function useAttachPeople(
    patientId: string,
    mode: 'create' | 'edit' = 'create'
) {
    const [professionals, setProfessionals] = useState<ProfessionalOption[]>([]);
    const [assignments, setAssignments] = useState<CareTeamAssignmentItem[]>([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // Charge les professionnels disponibles
    useEffect(() => {
        const loadProfessionals = async () => {
            try {
                const data = await fetchProfessionalsForAttach();
                setProfessionals(data);
            } catch (err) {
                setError('Impossible de charger les professionnels.');
            }
        };
        loadProfessionals();
    }, []);

    // Si mode édition, charge les affectations existantes
    useEffect(() => {
        if (mode === 'edit') {
            const loadExisting = async () => {
                try {
                    const existing = await fetchExistingAssignments(patientId);
                    setAssignments(existing);
                } catch (err) {
                    setError('Impossible de charger les affectations existantes.');
                }
            };
            loadExisting();
        }
    }, [mode, patientId]);

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

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            const payload: AttachPeopleFormValues = {
                patientId,
                assignments,
            };
            if (mode === 'edit') {
                // ✅ Appeler un service de mise à jour
                await updateAssignmentsForPatient(patientId, payload);
            } else {
                await attachProfessionalsToPatient(payload);
            }
            setAssignments([]);
        } catch (err) {
            setError('Erreur lors de l’enregistrement.');
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
