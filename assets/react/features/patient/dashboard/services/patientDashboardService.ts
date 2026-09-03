// services/patientDashboardService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import {
    PatientDashboardData,
    HealthMetric,
    NextAppointment,
    NextMedication,
    WatchItem,
} from '../types';

// Types des réponses API (partiels)
interface BloodGlucoseMeasurement {
    id: string;
    value: number;
    unit?: string;
    measuredAt?: string;
    createdAt?: string;
}

interface WeightMeasurement {
    id: string;
    valueKg: number;
    measuredAt?: string;
    createdAt?: string;
}

interface HbA1cMeasurement {
    id: string;
    valuePercent: number;
    measuredAt?: string;
    createdAt?: string;
}

interface Appointment {
    id: string;
    scheduledAt: string;
    reason?: string;
    professional?: {
        fullName?: string;
    } | null;
    status?: string;
}

interface Prescription {
    id: string;
    status: string;
    startDate?: string;
    endDate?: string;
    items?: Array<{
        medication?: {
            name?: string;
        };
    }>;
}

function getLatestDate(...dates: (string | undefined)[]): string | undefined {
    const valid = dates.filter(Boolean) as string[];
    if (valid.length === 0) return undefined;
    return valid.sort((a, b) => new Date(b).getTime() - new Date(a).getTime())[0];
}

export async function fetchPatientDashboard(): Promise<PatientDashboardData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    let profile = { fullName: 'Patient' };
    try {
        const response = await apiClient.get<ApiFeedback<{ fullName: string }>>(`/patients/${patientId}/profile`);
        profile = unwrapApiData(response.data, 'Erreur profil');
    } catch (e) {
        console.warn('Profil non chargé, valeur par défaut utilisée.');
    }

    // Mesures
    let lastGlucose, lastWeight, lastHba1c;
    try {
        const resp = await apiClient.get<ApiFeedback<any[]>>(`/patients/${patientId}/blood-glucose-measurements`);
        lastGlucose = unwrapApiData(resp.data)[0] ?? null;
    } catch (e) { console.warn('Glycémie non disponible.'); }

    try {
        const resp = await apiClient.get<ApiFeedback<any[]>>(`/patients/${patientId}/weight-measurements`);
        lastWeight = unwrapApiData(resp.data)[0] ?? null;
    } catch (e) { console.warn('Poids non disponible.'); }

    try {
        const resp = await apiClient.get<ApiFeedback<any[]>>(`/patients/${patientId}/hba1c-measurements`);
        lastHba1c = unwrapApiData(resp.data)[0] ?? null;
    } catch (e) { console.warn('HbA1c non disponible.'); }

    // Rendez-vous
    let nextAppointment: NextAppointment = { date: 'Aucun', time: '', doctor: '' };
    try {
        const resp = await apiClient.get<ApiFeedback<any[]>>(`/appointments/queries/patient/${patientId}`);
        const appointments = unwrapApiData(resp.data);
        const future = appointments
            .filter((a) => new Date(a.scheduledAt) > new Date())
            .sort((a, b) => new Date(a.scheduledAt).getTime() - new Date(b.scheduledAt).getTime());
        if (future.length > 0) {
            const a = future[0];
            nextAppointment = {
                date: new Date(a.scheduledAt).toLocaleDateString('fr-FR'),
                time: new Date(a.scheduledAt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                doctor: a.professional?.fullName ?? 'Non spécifié',
            };
        }
    } catch (e) { console.warn('Rendez-vous non disponible.'); }

    // Prescriptions
    let nextMedication: NextMedication = { time: 'Aucune', name: '' };
    try {
        const resp = await apiClient.get<ApiFeedback<any[]>>(`/prescriptions/patient/${patientId}`);
        const prescriptions = unwrapApiData(resp.data);
        const active = prescriptions.filter((p) => p.status === 'ACTIVE');
        if (active.length > 0 && active[0].items?.length > 0) {
            nextMedication = {
                time: 'Selon ordonnance',
                name: active[0].items[0].medication?.name ?? 'Non spécifié',
            };
        }
    } catch (e) { console.warn('Prescriptions non disponibles.'); }

    // Métriques
    const hba1cValue = lastHba1c?.valuePercent !== undefined ? Number(lastHba1c.valuePercent) : null;
    const hba1cDisplay = hba1cValue !== null && !isNaN(hba1cValue) ? hba1cValue.toFixed(1) : '--';

    const metrics: HealthMetric[] = [
        { id: 'glycemie', label: 'Glycémie', value: lastGlucose?.value?.toString() ?? '--', unit: lastGlucose?.unit ?? 'mg/dL' },
        { id: 'poids', label: 'Poids', value: lastWeight?.valueKg?.toString() ?? '--', unit: 'kg' },
        { id: 'hba1c', label: 'HbA1c', value: hba1cDisplay, unit: '%' },
    ];

    // Liste de surveillance
    const watchList: WatchItem[] = [];
    if (lastGlucose && lastGlucose.value > 180) watchList.push({ id: 'high-glucose', message: 'Glycémie élevée.' });
    if (nextAppointment.date !== 'Aucun') watchList.push({ id: 'appointment', message: `RDV le ${nextAppointment.date}` });
    if (!nextMedication.name) watchList.push({ id: 'no-med', message: 'Aucune prise prévue.' });

    return {
        patientName: profile.fullName || 'Patient',
        metrics,
        nextAppointment,
        nextMedication,
        watchList,
    };
}
