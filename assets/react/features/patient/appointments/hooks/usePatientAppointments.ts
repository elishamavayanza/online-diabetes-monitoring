// hooks/usePatientAppointments.ts
import { useCallback, useEffect, useState } from 'react';
import { fetchPatientAppointments } from '../services/patientAppointmentsService';
import { PatientAppointment } from '../types';

export function usePatientAppointments() {
    const [appointments, setAppointments] = useState<PatientAppointment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchPatientAppointments();
            setAppointments(data);
        } catch (err) {
            setError('Impossible de charger les rendez-vous.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    return { appointments, isLoading, error, reload: load };
}
