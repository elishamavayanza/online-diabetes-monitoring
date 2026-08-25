import { useEffect, useState } from 'react';
import { fetchPatientDashboard } from '../services/patientDashboardService';
import { PatientDashboardData } from '../types';

export function usePatientDashboard() {
    const [data, setData] = useState<PatientDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const result = await fetchPatientDashboard();
                setData(result);
            } catch (err) {
                setError('Impossible de charger le résumé de santé.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { data, isLoading, error };
}
