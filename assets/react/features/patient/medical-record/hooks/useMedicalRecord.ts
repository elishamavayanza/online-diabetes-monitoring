// hooks/useMedicalRecord.ts
import { useEffect, useState } from 'react';
import { fetchMedicalRecord } from '../services/medicalRecordService';
import { MedicalRecordData } from '../types';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';

export function useMedicalRecord() {
    const [data, setData] = useState<MedicalRecordData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const patientId = getCurrentUserIdFromToken();
        if (!patientId) {
            setError('Utilisateur non identifié.');
            setIsLoading(false);
            return;
        }

        const load = async () => {
            try {
                const result = await fetchMedicalRecord(patientId);
                setData(result);
            } catch (err) {
                console.error(err);
                setError('Impossible de charger le dossier.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
