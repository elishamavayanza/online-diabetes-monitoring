import { useEffect, useState } from 'react';
import { fetchMeasurements } from '../services/measurementsService';
import { MeasurementType, MeasurementRecord } from '../types';

export function useMeasurements() {
    const [type, setType] = useState<MeasurementType>('Glycémie');
    const [records, setRecords] = useState<MeasurementRecord[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchMeasurements(type);
                setRecords(data);
            } catch (err) {
                setError('Impossible de charger les mesures.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [type]);

    return { type, setType, records, isLoading, error };
}
