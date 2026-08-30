import { StatisticValue } from '../types';

const LABELS: Record<string, string> = {
    MALE: 'Homme',
    FEMALE: 'Femme',
    OTHER: 'Autre',
    UNSPECIFIED: 'Non renseigné',
    SCHEDULED: 'Planifié',
    CONFIRMED: 'Confirmé',
    COMPLETED: 'Terminé',
    CANCELLED: 'Annulé',
    NO_SHOW: 'Absent',
    RESCHEDULE_REQUESTED: 'Report demandé',
    TAKEN: 'Pris',
    SKIPPED: 'Oublié',
    DELAYED: 'Retardé',
    BREAKFAST: 'Petit-déjeuner',
    LUNCH: 'Déjeuner',
    DINNER: 'Dîner',
    SNACK: 'Collation',
};

export function formatLabel(key: string): string {
    return LABELS[key] ?? key.replace(/_/g, ' ').toLowerCase();
}

export function formatStatValue(stat: StatisticValue): string {
    if (stat.value === null || stat.value === undefined) {
        return '—';
    }

    const formatted = Number.isInteger(stat.value)
        ? stat.value.toString()
        : stat.value.toFixed(1);

    return stat.unit ? `${formatted} ${stat.unit}` : formatted;
}

export function formatChange(changePercent?: number | null): string {
    if (changePercent === null || changePercent === undefined) {
        return '—';
    }

    const sign = changePercent > 0 ? '+' : '';
    return `${sign}${changePercent}%`;
}

export function changeClass(changePercent?: number | null): string {
    if (changePercent === null || changePercent === undefined || changePercent === 0) {
        return 'neutral';
    }

    return changePercent > 0 ? 'positive' : 'negative';
}
