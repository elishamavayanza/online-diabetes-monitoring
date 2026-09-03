// hooks/useDoses.ts
import { useCallback, useEffect, useState } from 'react';
import { fetchDoses, recordIntake as recordIntakeApi } from '../services/dosesService';
import { MedicationIntake, IntakeStatus } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useDoses() {
    const { showToast } = useToast();
    const [intakes, setIntakes] = useState<MedicationIntake[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchDoses();
            setIntakes(data.today);
        } catch (err) {
            setError('Impossible de charger les prises.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    const recordIntake = useCallback(async (
        prescriptionItemId: string,
        status: IntakeStatus,
        takenAt: string,
        quantityTaken: string
    ) => {
        try {
            await recordIntakeApi({ prescriptionItemId, takenAt, quantityTaken, status });
            showToast({ type: 'success', message: 'Prise enregistrée.' });
            await load();
        } catch (err) {
            showToast({ type: 'error', message: "Erreur lors de l'enregistrement." });
        }
    }, [load, showToast]);

    return { intakes, isLoading, error, recordIntake, reload: load };
}
