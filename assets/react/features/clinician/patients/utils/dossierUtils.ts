import { TrendSeries } from '@/react/features/admin/reports/types';
import { CalendarMarkedDate } from '@/react/hook-components/Calendars/Calendar';
import { DossierTabId, MeasurementPeriod, PatientDossierData } from '../types';
import { CandlestickDataPoint } from '@/react/hook-components/Data/CandlestickChart/useCandlestickChart';
import {BadgeVariant} from "@/react/hook-components/UI/Badge"; // ✅ import

export function toDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

export function formatDisplayDate(iso: string): string {
    return new Date(iso).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

export function formatDisplayDateTime(iso: string): string {
    return new Date(iso).toLocaleString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function getPeriodStart(period: MeasurementPeriod): Date | null {
    if (period === 'all') return null;
    const now = new Date();
    const days = period === '7d' ? 7 : period === '30d' ? 30 : 90;
    const start = new Date(now);
    start.setDate(start.getDate() - days);
    start.setHours(0, 0, 0, 0);
    return start;
}

export function isInPeriod(iso: string, period: MeasurementPeriod, selectedDate?: Date | null): boolean {
    const date = new Date(iso);
    if (selectedDate) {
        return toDateKey(date) === toDateKey(selectedDate);
    }
    const start = getPeriodStart(period);
    if (!start) return true;
    return date >= start;
}

export function buildTrendSeries(
    label: string,
    items: { createdAt: string; value: number }[],
    period: MeasurementPeriod,
    selectedDate?: Date | null,
    unit?: string,
): TrendSeries {
    const filtered = items
        .filter((item) => isInPeriod(item.createdAt, period, selectedDate))
        .sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());

    return {
        label,
        unit,
        points: filtered.map((item) => ({
            date: formatDisplayDate(item.createdAt),
            value: item.value,
        })),
    };
}

export function collectMarkedDates(
    appointments: { scheduledAt: string }[],
    measurements: { createdAt: string }[],
): CalendarMarkedDate[] {
    const dateKeys = new Set<string>();

    appointments.forEach((appt) => {
        dateKeys.add(toDateKey(new Date(appt.scheduledAt)));
    });

    measurements.forEach((m) => {
        dateKeys.add(toDateKey(new Date(m.createdAt)));
    });

    return Array.from(dateKeys).map((dateKey) => ({
        date: new Date(`${dateKey}T12:00:00`),
        type: 'info' as const,
    }));
}

function addDateKey(keys: Set<string>, iso?: string) {
    if (!iso) return;
    keys.add(toDateKey(new Date(iso)));
}

/** Marqueurs du calendrier limités aux données de l'onglet actif. */
export function collectMarkedDatesForTab(
    tab: DossierTabId,
    data: PatientDossierData,
): CalendarMarkedDate[] {
    const dateKeys = new Set<string>();

    switch (tab) {
        case 'measurements':
            Object.values(data.measurements).forEach((items) => {
                items.forEach((item) => addDateKey(dateKeys, item.createdAt));
            });
            break;

        case 'prescriptions':
            data.prescriptions.forEach((rx) => {
                addDateKey(dateKeys, rx.startDate);
                addDateKey(dateKeys, rx.endDate);
            });
            break;

        case 'consultations':
            data.appointments.forEach((appt) => addDateKey(dateKeys, appt.scheduledAt));
            data.notes.forEach((note) => addDateKey(dateKeys, note.notedAt ?? note.createdAt));
            break;

        case 'nutrition':
            data.meals.forEach((meal) => addDateKey(dateKeys, meal.measuredAt ?? meal.createdAt));
            break;

        case 'appointments':
            data.appointments.forEach((appt) => addDateKey(dateKeys, appt.scheduledAt));
            break;

        case 'notes':
            data.notes.forEach((note) => addDateKey(dateKeys, note.notedAt ?? note.createdAt));
            break;

        case 'medical-profile':
            data.diagnoses.forEach((diag) => addDateKey(dateKeys, diag.diagnosedAt));
            data.consents.forEach((consent) => {
                addDateKey(dateKeys, consent.grantedAt);
                addDateKey(dateKeys, consent.revokedAt);
            });
            break;

        case 'overview':
        case 'communications':
        default:
            break;
    }

    return Array.from(dateKeys).map((dateKey) => ({
        date: new Date(`${dateKey}T12:00:00`),
        type: 'info' as const,
    }));
}

// ✅ Nouvelle fonction pour transformer les mesures en chandeliers
export function buildCandlestickData(items: { createdAt: string; value: number }[]): CandlestickDataPoint[] {
    if (items.length === 0) return [];

    // Trier par date croissante
    const sorted = [...items].sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime());

    // Regrouper par jour (YYYY-MM-DD)
    const grouped = new Map<string, { createdAt: string; value: number }[]>();

    sorted.forEach((item) => {
        const day = item.createdAt.slice(0, 10);
        if (!grouped.has(day)) {
            grouped.set(day, []);
        }
        grouped.get(day)!.push(item);
    });

    const result: CandlestickDataPoint[] = [];

    grouped.forEach((dayItems, day) => {
        const values = dayItems.map((d) => d.value);
        result.push({
            date: day,
            open: values[0],
            high: Math.max(...values),
            low: Math.min(...values),
            close: values[values.length - 1],
        });
    });

    return result;
}
export function getAppointmentStatusBadgeVariant(status: string): BadgeVariant {
    switch (status) {
        case 'COMPLETED':
            return 'success';
        case 'CONFIRMED':
            return 'info';
        case 'CANCELLED':
            return 'error';          // au lieu de 'danger'
        case 'NO_SHOW':
            return 'error';          //
        case 'RESCHEDULE_REQUESTED':
            return 'warning';
        case 'SCHEDULED':
        default:
            return 'info';
    }
}
