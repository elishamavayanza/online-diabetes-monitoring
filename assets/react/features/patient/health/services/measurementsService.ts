// services/measurementsService.ts
import { getDateFormatLocale } from '@/react/i18n/dateLocale';
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { MeasurementType, MeasurementRecord } from '../types';

// Interfaces pour les réponses API
interface BloodGlucoseResponse {
    id: string;
    value: number;
    unit?: string;
    context?: string;
    measuredAt?: string;
    createdAt?: string;
}

interface BloodPressureResponse {
    id: string;
    systolic: number;
    diastolic: number;
    pulse?: number;
    measuredAt?: string;
    createdAt?: string;
}

interface WeightResponse {
    id: string;
    valueKg: string | number;
    bmi?: number;
    measuredAt?: string;
    createdAt?: string;
}

interface HbA1cResponse {
    id: string;
    valuePercent: string | number;
    measuredAt?: string;
    createdAt?: string;
}

interface PhysicalActivityResponse {
    id: string;
    activityType?: string;
    durationMinutes: number;
    caloriesBurned?: number;
    measuredAt?: string;
    createdAt?: string;
}

interface InsulinInjectionResponse {
    id: string;
    injectedAt?: string;
    createdAt?: string;
    doseUnits: string | number;
    injectionSite?: string;
    status?: string;
    notes?: string;
}

const INJECTION_STATUS_LABELS: Record<string, string> = {
    TAKEN: 'Réalisée',
    SKIPPED: 'Sautée',
    DELAYED: 'En retard',
};

const INJECTION_SITE_LABELS: Record<string, string> = {
    ABDOMEN: 'Abdomen',
    THIGH: 'Cuisse',
    UPPER_ARM: 'Haut du bras',
    BUTTOCK: 'Fesse',
    OTHER: 'Autre',
};

function formatDate(dateStr?: string): string {
    if (!dateStr) return new Date().toLocaleDateString(getDateFormatLocale());
    return new Date(dateStr).toLocaleDateString(getDateFormatLocale());
}

function formatDateTime(dateStr?: string): string {
    if (!dateStr) return new Date().toLocaleString('fr-FR');
    return new Date(dateStr).toLocaleString('fr-FR');
}

export async function fetchMeasurements(type: MeasurementType): Promise<MeasurementRecord[]> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) {
        throw new Error('Utilisateur non identifié.');
    }

    switch (type) {
        case 'Glycémie': {
            const response = await apiClient.get<ApiFeedback<BloodGlucoseResponse[]>>(
                `/patients/${patientId}/blood-glucose-measurements`
            );
            const measurements = unwrapApiData(response.data, 'Erreur lors du chargement des glycémies.');
            return measurements.map((m) => ({
                id: m.id,
                date: m.measuredAt ?? m.createdAt ?? '',
                value: `${m.value} ${m.unit ?? 'mg/dL'}`,
                note: m.context ?? '',
            }));
        }

        case 'Tension': {
            const response = await apiClient.get<ApiFeedback<BloodPressureResponse[]>>(
                `/patients/${patientId}/blood-pressure-measurements`
            );
            const measurements = unwrapApiData(response.data, 'Erreur lors du chargement des tensions.');
            return measurements.map((m) => ({
                id: m.id,
                date: m.measuredAt ?? m.createdAt ?? '',
                value: `${m.systolic}/${m.diastolic} mmHg`,
                note: m.pulse != null ? `Pouls: ${m.pulse}` : '',
            }));
        }

        case 'Poids': {
            const response = await apiClient.get<ApiFeedback<WeightResponse[]>>(
                `/patients/${patientId}/weight-measurements`
            );
            const measurements = unwrapApiData(response.data, 'Erreur lors du chargement des poids.');
            return measurements.map((m) => ({
                id: m.id,
                date: m.measuredAt ?? m.createdAt ?? '',
                value: `${Number(m.valueKg).toFixed(2)} kg`,
                note: m.bmi != null ? `IMC: ${Number(m.bmi).toFixed(1)}` : '',
            }));
        }

        case 'HbA1c': {
            const response = await apiClient.get<ApiFeedback<HbA1cResponse[]>>(
                `/patients/${patientId}/hba1c-measurements`
            );
            const measurements = unwrapApiData(response.data, 'Erreur lors du chargement des HbA1c.');
            return measurements.map((m) => ({
                id: m.id,
                date: m.measuredAt ?? m.createdAt ?? '',
                value: `${Number(m.valuePercent).toFixed(1)} %`,
                note: '',
            }));
        }

        case 'Activité': {
            const response = await apiClient.get<ApiFeedback<PhysicalActivityResponse[]>>(
                `/patients/${patientId}/physical-activity-measurements`
            );
            const measurements = unwrapApiData(response.data, 'Erreur lors du chargement des activités.');
            return measurements.map((m) => ({
                id: m.id,
                date: m.measuredAt ?? m.createdAt ?? '',
                value: `${m.durationMinutes} min`,
                note: m.activityType ?? '',
            }));
        }

        case 'Injection': {
            const response = await apiClient.get<ApiFeedback<InsulinInjectionResponse[]>>(
                `/insulin-injections/patient/${patientId}`
            );
            const injections = unwrapApiData(response.data, 'Erreur lors du chargement des injections.');
            return injections.map((m) => {
                const parts = [
                    m.status ? (INJECTION_STATUS_LABELS[m.status] ?? m.status) : '',
                    m.injectionSite ? (INJECTION_SITE_LABELS[m.injectionSite] ?? m.injectionSite) : '',
                ].filter(Boolean);
                const note = m.notes ? [...parts, m.notes].join(' · ') : parts.join(' · ');
                return {
                    id: m.id,
                    date: m.injectedAt ?? m.createdAt ?? '',
                    value: `${Number(m.doseUnits)} u`,
                    note,
                };
            });
        }

        default:
            return [];
    }
}
