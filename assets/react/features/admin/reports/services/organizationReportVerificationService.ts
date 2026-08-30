import apiClient from '@/services/api/client';

export interface ReportVerificationResult {
    authentic: boolean;
    reference: string;
    organizationId: string;
    organizationName: string;
    periodFrom: string;
    periodTo: string;
    documentType: string;
    verifiedAt: string;
    message?: string | null;
}

interface ApiFeedback<T> {
    status: number;
    data: T;
}

export async function verifyOrganizationReport(params: {
    ref: string;
    organizationId: string;
    from: string;
    to: string;
}): Promise<ReportVerificationResult> {
    const response = await apiClient.get<ApiFeedback<ReportVerificationResult>>(
        '/v1/organization/reports/verify',
        { params }
    );

    return response.data.data;
}
