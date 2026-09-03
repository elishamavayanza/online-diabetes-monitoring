import { useEffect, useState, useCallback } from 'react';
import { fetchTreatments, stopTreatment as stopTreatmentApi } from '../services/treatmentsService';
import { Treatment } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useTreatments() {
    const { showToast } = useToast();
    const [treatments, setTreatments] = useState<Treatment[]>([]);
    const [pastTreatments, setPastTreatments] = useState<Treatment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchTreatments();
            setTreatments(data.treatments);
            setPastTreatments(data.pastTreatments);
        } catch (err) {
            console.error(err);
            setError('Impossible de charger les traitements.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    const stopTreatment = useCallback(async (prescriptionId: string, reason?: string) => {
        try {
            await stopTreatmentApi(prescriptionId, reason);
            showToast({ type: 'success', message: 'Traitement arrêté avec succès.' });
            await load();
        } catch (err) {
            const message = err instanceof Error ? err.message : "Impossible d'arrêter le traitement.";
            showToast({ type: 'error', message });
        }
    }, [load, showToast]);

    return { treatments, pastTreatments, stopTreatment, isLoading, error };
}
