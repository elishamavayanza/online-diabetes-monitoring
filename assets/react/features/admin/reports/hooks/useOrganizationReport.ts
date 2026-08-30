import { useCallback, useEffect, useState } from 'react';
import { fetchOrganizationReport } from '../services/organizationReportService';
import { OrganizationReport, PeriodPreset, ReportFilters } from '../types';
import { validateReportFilters } from '../utils/reportFilters';

export function useOrganizationReport(initialPreset: PeriodPreset = 'month') {
    const [filters, setFilters] = useState<ReportFilters>({ period: initialPreset });
    const [data, setData] = useState<OrganizationReport | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async (nextFilters: ReportFilters) => {
        const periodError = validateReportFilters(nextFilters);
        if (periodError) {
            return;
        }

        setIsLoading(true);
        setError(null);

        try {
            const result = await fetchOrganizationReport(nextFilters);
            setData(result);
        } catch {
            setError('Impossible de charger le rapport organisation.');
            setData(null);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load(filters);
    }, [filters, load]);

    const setPeriod = (period: PeriodPreset) => {
        setFilters({ period });
    };

    const setCustomRange = (from: string, to: string) => {
        setFilters({ from, to });
    };

    return { data, isLoading, error, filters, setPeriod, setCustomRange, reload: () => load(filters) };
}
