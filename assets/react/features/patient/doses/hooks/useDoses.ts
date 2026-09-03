import { useCallback, useEffect, useState } from 'react';
import { fetchDoses, recordIntake as recordIntakeApi } from '../services/dosesService';
import { MedicationIntake, IntakeStatus } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useDoses() {
    const { showToast } = useToast();
    const [intakes, setIntakes] = useState<MedicationIntake[]>([]);
    const [selectedDate, setSelectedDate] = useState<Date>(new Date());
    const [markedDates, setMarkedDates] = useState<{ date: Date }[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchDoses(selectedDate);
            setIntakes(data.today);
            setMarkedDates(data.markedDates ?? []);
        } catch (err) {
            setError('Impossible de charger les prises.');
        } finally {
            setIsLoading(false);
        }
    }, [selectedDate]);

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

    return { intakes, selectedDate, setSelectedDate, markedDates, isLoading, error, recordIntake, reload: load };
}
