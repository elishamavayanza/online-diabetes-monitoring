import apiClient from '@/services/api/client';
import { OrganizationReport, ReportFilters } from '../types';

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

export async function fetchOrganizationReport(filters: ReportFilters = {}): Promise<OrganizationReport> {
    const params: Record<string, string> = {};

    if (filters.period) {
        params.period = filters.period;
    }
    if (filters.from) {
        params.from = filters.from;
    }
    if (filters.to) {
        params.to = filters.to;
    }

    const response = await apiClient.get<ApiFeedback<OrganizationReport>>('/v1/organization/reports', { params });

    if (!response.data?.data) {
        throw new Error('Rapport indisponible.');
    }

    return response.data.data;
}
