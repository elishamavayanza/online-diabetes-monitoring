import { useEffect, useState } from 'react';
import { fetchClinicianPatients } from '../services/clinicianPatientsService';
import { ClinicianPatient } from '../types';

export function useClinicianPatients() {
    const [patients, setPatients] = useState<ClinicianPatient[]>([]);
    const [search, setSearch] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchClinicianPatients(search);
                setPatients(data);
            } catch (err) {
                setError('Impossible de charger les patients.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [search]);

    return { patients, search, setSearch, isLoading, error };
}
