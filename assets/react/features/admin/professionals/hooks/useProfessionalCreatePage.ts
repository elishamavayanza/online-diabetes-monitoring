import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCreateProfessional } from '@/react/features/admin/professionals/hooks/useCreateProfessional';
import {
    fetchPatientsForAssignment,
    assignPatientToProfessional,
} from '@/react/features/admin/professionals/services/careTeamService';
import { fetchProfessionals } from '@/react/features/admin/professionals/services/professionalsService';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface PatientOption {
    id: string;
    nom: string;
}

export function useProfessionalCreatePage() {
    const navigate = useNavigate();
    const { showToast } = useToast();
    const { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error } =
        useCreateProfessional();

    const [step, setStep] = useState(0);
    const [patients, setPatients] = useState<PatientOption[]>([]);
    const [selectedPatients, setSelectedPatients] = useState<string[]>([]);
    const [isLoadingPatients, setIsLoadingPatients] = useState(false);
    const [patientsError, setPatientsError] = useState<string | null>(null);
    const [isAttaching, setIsAttaching] = useState(false); // état pour l'attachement

    const steps = [
        { id: 'infos-address', label: 'Informations & Adresse' },
        { id: 'photo', label: 'Photo' },
        { id: 'attach-patients', label: 'Attacher des patients' },
        { id: 'summary', label: 'Récapitulatif' },
    ];

    const activeStepId = steps[step].id;

    useEffect(() => {
        if (step === 2) {
            setIsLoadingPatients(true);
            setPatientsError(null);
            fetchPatientsForAssignment()
                .then((data) => setPatients(data))
                .catch((err) => {
                    console.error('Erreur chargement patients:', err);
                    setPatientsError('Impossible de charger les patients.');
                })
                .finally(() => setIsLoadingPatients(false));
        }
    }, [step]);

    const handleStepChange = (newStepId: string) => {
        const index = steps.findIndex((s) => s.id === newStepId);
        if (index >= 0) setStep(index);
    };

    const handleNext = () => setStep((prev) => Math.min(prev + 1, steps.length - 1));
    const handlePrev = () => setStep((prev) => Math.max(prev - 1, 0));

    const togglePatient = (id: string) => {
        setSelectedPatients((prev) =>
            prev.includes(id) ? prev.filter((p) => p !== id) : [...prev, id]
        );
    };

    const toggleAllPatients = () => {
        if (selectedPatients.length === patients.length) {
            setSelectedPatients([]);
        } else {
            setSelectedPatients(patients.map((p) => p.id));
        }
    };

    // Récupérer l'ID de l'organisation depuis le token
    const getOrganizationId = (): string | null => {
        const token = tokenStorage.getAccessToken();
        if (!token) return null;
        try {
            const payload = decodeJwtPayload(token);
            const orgs = payload?.organizations;
            if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
                return String(orgs[0].organization_id);
            }
        } catch (e) {
            console.error('Erreur décodage token:', e);
        }
        return null;
    };

    const handleSubmit = async () => {
        // 1. Créer le professionnel et récupérer directement son ID renvoyé par le backend
        const professionalId = await submit();
        if (!professionalId) {
            // Le toast d'erreur a déjà été affiché par useCreateProfessional
            return;
        }

        // 2. Attacher les patients sélectionnés (si il y en a)
        if (selectedPatients.length > 0) {
            const organizationId = getOrganizationId();
            if (!organizationId) {
                showToast({
                    type: 'error',
                    message: 'Organisation introuvable, impossible d\'attacher les patients.',
                });
                navigate('/admin/professionals');
                return;
            }

            setIsAttaching(true);
            try {
                const startDate = new Date().toISOString().split('T')[0]; // format YYYY-MM-DD
                for (const patientId of selectedPatients) {
                    await assignPatientToProfessional(organizationId, {
                        patientId: Number(patientId),
                        professionalId: Number(professionalId),
                        role: 'PRIMARY_CLINICIAN',
                        startDate,
                        endDate: null,
                        active: true,
                    });
                }
                showToast({
                    type: 'success',
                    message: `${selectedPatients.length} patient(s) attaché(s) avec succès.`,
                });
            } catch (err) {
                console.error('Erreur attachement patients:', err);
                showToast({
                    type: 'error',
                    message: 'Certains patients n\'ont pas pu être attachés.',
                });
            } finally {
                setIsAttaching(false);
            }
        }

        navigate('/admin/professionals');
    };

    return {
        form,
        updateField,
        updateAddress,
        updateAvatar,
        submit: handleSubmit,
        isSubmitting,
        error,
        step,
        setStep,
        patients,
        selectedPatients,
        isLoadingPatients,
        patientsError,
        steps,
        activeStepId,
        handleStepChange,
        handleNext,
        handlePrev,
        togglePatient,
        toggleAllPatients,
        isAttaching, // pour éventuellement désactiver les boutons
    };
}
