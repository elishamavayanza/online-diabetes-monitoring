import { useCallback, useEffect, useState } from 'react';
import {
    fetchMedicalRecord,
    createMedicalRecord,
    reopenMedicalRecord,
} from '../services/medicalRecordService';
import { MedicalRecord } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useMedicalRecord(patientId: string) {
    const [record, setRecord] = useState<MedicalRecord | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchMedicalRecord(patientId);
            setRecord(data);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger le dossier médical.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [patientId, showToast]);

    useEffect(() => {
        load();
    }, [load]);

    const create = async () => {
        setIsSaving(true);
        setError(null);
        try {
            const newRecord = await createMedicalRecord(patientId);
            setRecord(newRecord);
            showToast({ type: 'success', message: 'Dossier médical créé avec succès.' });
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la création.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSaving(false);
        }
    };

    const reopen = async () => {
        setIsSaving(true);
        setError(null);
        try {
            const updated = await reopenMedicalRecord(patientId);
            setRecord(updated);
            showToast({ type: 'success', message: 'Dossier médical réouvert avec succès.' });
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la réouverture.';
            setError(message);
            showToast({ type: 'error', message });
            return false;
        } finally {
            setIsSaving(false);
        }
    };

    return { record, isLoading, isSaving, error, load, create, reopen };
}
