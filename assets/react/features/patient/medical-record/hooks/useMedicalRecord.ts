import { useEffect, useState } from 'react';
import { fetchMedicalRecord } from '../services/medicalRecordService';
import { MedicalRecordData } from '../types';

export function useMedicalRecord() {
    const [data, setData] = useState<MedicalRecordData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchMedicalRecord();
                setData(result);
            } catch (err) {
                setError('Impossible de charger le dossier.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
