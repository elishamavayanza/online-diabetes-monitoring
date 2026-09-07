import { useEffect, useState } from 'react';
import { createMeasurement, createLaboratoryResultWithFile } from '../services/dossierActionsService';
import { MeasurementTypeId } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface UseMeasurementFormProps {
    isOpen: boolean;
    onClose: () => void;
    patientId: string;
    initialType?: MeasurementTypeId;
    onSuccess: () => void;
}

function getCurrentDateTimeLocal(): string {
    const now = new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
}

export function useMeasurementForm({
                                       isOpen,
                                       onClose,
                                       patientId,
                                       initialType,
                                       onSuccess,
                                   }: UseMeasurementFormProps) {
    const { showToast } = useToast();
    const [step, setStep] = useState<'type' | 'form'>('type');
    const [type, setType] = useState<MeasurementTypeId | null>(initialType ?? null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState<Record<string, string>>({});

    useEffect(() => {
        if (!isOpen) {
            setStep(initialType ? 'form' : 'type');
            setType(initialType ?? null);
            setForm({});
            setError(null);
        } else if (initialType) {
            setType(initialType);
            setStep('form');
            setForm(
                initialType === 'insulinInjection'
                    ? { injectedAt: getCurrentDateTimeLocal() }
                    : { measuredAt: getCurrentDateTimeLocal() },
            );
        }
    }, [isOpen, initialType]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSelectType = (selected: MeasurementTypeId) => {
        setType(selected);
        setStep('form');
        setForm(
            selected === 'insulinInjection'
                ? { injectedAt: getCurrentDateTimeLocal() }
                : { measuredAt: getCurrentDateTimeLocal() },
        );
    };

    const handleSubmit = async (e: React.FormEvent, labFile?: File | null) => {
        e.preventDefault();
        if (!type) return;

        setIsLoading(true);
        setError(null);
        try {
            // Cas spécial : laboratoire avec upload
            if (type === 'laboratory' && labFile) {
                const fd = new FormData();
                fd.append('testName', form.testName ?? '');
                if (form.labName) fd.append('labName', form.labName);
                // ⚠️ Ne pas envoyer measuredAt car le backend ne l'attend pas dans ce formulaire
                fd.append('file', labFile);
                await createLaboratoryResultWithFile(patientId, fd);
                showToast({ type: 'success', message: 'Résultat de laboratoire enregistré avec succès.' });
                onSuccess();
                onClose();
                return;
            }

            let payload: Record<string, unknown> = { ...form };

            if (type === 'physicalActivity') {
                payload = { ...form, durationMinutes: Number(form.durationMinutes) };
            }
            if (type === 'bloodGlucose' && !form.unit) {
                payload.unit = 'MG_DL';
            }
            if (type === 'insulinInjection') {
                payload = {
                    ...form,
                    doseUnits: form.doseUnits ?? '',
                    injectionSite: form.injectionSite ?? 'ABDOMEN',
                    status: form.status ?? 'TAKEN',
                };
            }
            // Convertir measuredAt en ISO si présent (pour les autres types)
            if (payload.measuredAt) {
                payload.measuredAt = new Date(payload.measuredAt as string).toISOString();
            }
            // Convertir injectedAt en ISO pour les injections d'insuline
            if (payload.injectedAt) {
                payload.injectedAt = new Date(payload.injectedAt as string).toISOString();
            }

            await createMeasurement(patientId, type, payload);
            showToast({
                type: 'success',
                message: type === 'insulinInjection'
                    ? 'Injection d\'insuline enregistrée avec succès.'
                    : 'Mesure enregistrée avec succès.',
            });
            onSuccess();
            onClose();
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors du prélèvement.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    };

    return {
        step,
        type,
        isLoading,
        error,
        form,
        handleChange,
        handleSelectType,
        handleSubmit,
        setStep,
    };
}
