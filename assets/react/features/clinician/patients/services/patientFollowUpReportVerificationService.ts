import apiClient from '@/services/api/client';

export interface PatientReportVerificationResult {
    authentic: boolean;
    reference: string;
    patientId: string;
    patientFullName: string;
    organizationName?: string | null;
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

export async function verifyPatientFollowUpReport(params: {
    ref: string;
    patientId: string;
    from: string;
    to: string;
}): Promise<PatientReportVerificationResult> {
    const response = await apiClient.get<ApiFeedback<PatientReportVerificationResult>>(
        '/v1/patient-reports/follow-up/verify',
        { params },
    );

    return response.data.data;
}
