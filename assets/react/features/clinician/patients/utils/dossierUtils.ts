import { TrendSeries } from '@/react/features/admin/reports/types';
import { MeasurementPeriod } from '../types';

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
): { date: Date; markers: string[] }[] {
    const map = new Map<string, Set<string>>();

    appointments.forEach((appt) => {
        const key = toDateKey(new Date(appt.scheduledAt));
        if (!map.has(key)) map.set(key, new Set());
        map.get(key)!.add('has-appointment');
    });

    measurements.forEach((m) => {
        const key = toDateKey(new Date(m.createdAt));
        if (!map.has(key)) map.set(key, new Set());
        map.get(key)!.add('has-measurement');
    });

    return Array.from(map.entries()).map(([dateKey, markers]) => ({
        date: new Date(dateKey),
        markers: Array.from(markers),
    }));
}
