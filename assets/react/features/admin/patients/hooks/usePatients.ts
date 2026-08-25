import { useEffect, useState } from 'react';
import { fetchPatients } from '../services/patientsService';
import { Patient, PatientsFilters } from '../types';

export function usePatients() {
    const [patients, setPatients] = useState<Patient[]>([]);
    const [filters, setFilters] = useState<PatientsFilters>({ search: '', typeDiabete: 'Tous' });
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchPatients(filters);
                setPatients(data);
            } catch (err) {
                setError('Impossible de charger les patients.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [filters]);

    return { patients, filters, setFilters, isLoading, error };
}
