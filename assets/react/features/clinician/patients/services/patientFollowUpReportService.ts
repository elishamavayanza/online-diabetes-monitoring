import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import {
    FollowUpReportElementId,
    FollowUpReportFilters,
    PatientFollowUpReport,
} from '../types/followUpReport';

export async function fetchPatientFollowUpReport(
    patientId: string,
    filters: FollowUpReportFilters,
    elements: FollowUpReportElementId[],
): Promise<PatientFollowUpReport> {
    const searchParams = new URLSearchParams();
    searchParams.set('from', filters.from);
    searchParams.set('to', filters.to);
    elements.forEach((element) => searchParams.append('elements[]', element));

    const response = await apiClient.get<ApiFeedback<PatientFollowUpReport>>(
        `/patients/${patientId}/reports/follow-up?${searchParams.toString()}`,
    );

    return unwrapApiData(response.data, 'Impossible de générer le rapport de suivi.');
}
