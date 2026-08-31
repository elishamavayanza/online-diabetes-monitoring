import { useCallback, useEffect, useState } from 'react';
import { fetchPatientDossier } from '../services/patientDossierService';
import { PatientDossierData } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function usePatientDossier(patientId: string) {
    const [data, setData] = useState<PatientDossierData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const dossier = await fetchPatientDossier(patientId);
            setData(dossier);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger le dossier patient.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [patientId, showToast]);

    useEffect(() => {
        load();
    }, [load]);

    return { data, isLoading, error, reload: load };
}
