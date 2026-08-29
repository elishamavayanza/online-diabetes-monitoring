import { OrgAdminFormValues } from '../types/orgAdmin';
import apiClient from "@/services/api/client";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

export async function createOrgAdmin(
    organizationId: string,
    payload: OrgAdminFormValues
): Promise<void> {
    const response = await apiClient.post<ApiFeedback<unknown>>(
        `/healthcare-organizations/${organizationId}/administrators`,
        payload
    );

    if (response.data.error) {
        throw new Error(
            response.data.message || "Erreur lors de la création de l'administrateur"
        );
    }
}
