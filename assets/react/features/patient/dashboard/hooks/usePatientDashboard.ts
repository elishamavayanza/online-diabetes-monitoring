import { useApiQuery } from '@/react/hooks/useApiQuery';
import { fetchPatientDashboard } from '../services/patientDashboardService';
import { PatientDashboardData } from '../types';

export function usePatientDashboard() {
    return useApiQuery<PatientDashboardData | null>(
        () => fetchPatientDashboard(),
        { fallbackMessage: 'Impossible de charger le résumé de santé.' },
    );
}