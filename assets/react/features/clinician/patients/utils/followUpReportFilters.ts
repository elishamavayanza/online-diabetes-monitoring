import { FollowUpReportFilters } from '../types/followUpReport';

export function validateFollowUpReportFilters(filters: FollowUpReportFilters): string | null {
    if (!filters.from || !filters.to) {
        return 'Veuillez renseigner une date de début et une date de fin.';
    }

    if (filters.from > filters.to) {
        return 'La date de début doit être antérieure ou égale à la date de fin.';
    }

    return null;
}

export function getFollowUpPeriodSummary(filters: FollowUpReportFilters): string {
    if (filters.from && filters.to) {
        return `Du ${filters.from} au ${filters.to}`;
    }

    return 'Période non définie';
}
