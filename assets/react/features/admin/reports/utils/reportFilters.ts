import { OrganizationReport, PeriodPreset, ReportFilters } from '../types';

export function getInitialReportFilters(report: OrganizationReport): ReportFilters {
    if (report.period.preset === 'custom') {
        return {
            from: report.period.from,
            to: report.period.to,
        };
    }

    if (report.period.preset === 'month' || report.period.preset === 'quarter' || report.period.preset === 'year') {
        return { period: report.period.preset };
    }

    return { period: 'month' };
}

export function validateReportFilters(filters: ReportFilters): string | null {
    if (filters.period) {
        return null;
    }

    if (!filters.from || !filters.to) {
        return 'Veuillez renseigner une date de début et une date de fin.';
    }

    if (filters.from > filters.to) {
        return 'La date de début doit être antérieure ou égale à la date de fin.';
    }

    return null;
}

export function getPeriodSummary(filters: ReportFilters): string {
    if (filters.period === 'month') {
        return 'Mois en cours';
    }

    if (filters.period === 'quarter') {
        return 'Trimestre en cours';
    }

    if (filters.period === 'year') {
        return 'Année en cours';
    }

    if (filters.from && filters.to) {
        return `Du ${filters.from} au ${filters.to}`;
    }

    return 'Période non définie';
}

export function isCustomPeriod(filters: ReportFilters): boolean {
    return !filters.period && Boolean(filters.from && filters.to);
}
