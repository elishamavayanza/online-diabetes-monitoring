// hooks/useMeasurements.ts
import { useEffect, useState, useCallback } from 'react';
import { fetchMeasurements } from '../services/measurementsService';
import { MeasurementType, MeasurementRecord } from '../types';

export function useMeasurements() {
    const [type, setType] = useState<MeasurementType>('Glycémie');
    const [records, setRecords] = useState<MeasurementRecord[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async (selectedType: MeasurementType) => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchMeasurements(selectedType);
            setRecords(data);
        } catch (err) {
            setError('Impossible de charger les mesures.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load(type);
    }, [type, load]);

    const refetch = useCallback(() => {
        load(type);
    }, [load, type]);

    return { type, setType, records, isLoading, error, refetch };
}
