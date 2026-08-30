import apiClient from "@/services/api/client";
import { Professional, ProfessionalFormValues } from "@/react/features/admin/professionals/types/types";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

// Mapper une réponse API vers l'objet Professional du front
function mapApiToProfessional(apiData: any): Professional {
    const fullName =
        apiData.fullName ??
        apiData.name ??
        (apiData.firstName && apiData.lastName
            ? `${apiData.firstName} ${apiData.lastName}`
            : '');

    let type: Professional['type'] = 'Clinician';
    if (apiData.professionalType === 'NUTRITIONIST' || apiData.type === 'NUTRITIONIST') {
        type = 'Nutritionist';
    } else if (apiData.professionalType === 'CLINICIAN' || apiData.type === 'CLINICIAN') {
        type = 'Clinician';
    }

    const statut = apiData.active === false ? 'Inactive' : 'Active';

    const etablissement =
        apiData.establishment?.name ??
        apiData.organization?.name ??
        apiData.facility?.name ??
        'Non assigné';

    const departement =
        apiData.department?.name ??
        apiData.service?.name ??
        apiData.specialty ??
        '';

    return {
        id: String(apiData.id ?? ''),
        nom: fullName,
        type,
        specialite: apiData.specialty ?? '',
        etablissement,
        departement,
        statut,
    };
}

// Mapper une réponse API vers ProfessionalFormValues (pour le formulaire d'édition)
function mapApiToProfessionalFormValues(apiData: any): ProfessionalFormValues {
    return {
        email: apiData.email ?? '',
        password: '', // laisser vide, pas de modification directe
        fullName: apiData.fullName ?? '',
        phone: apiData.phone ?? '',
        gender: apiData.gender ?? 'UNSPECIFIED',
        locale: apiData.locale ?? 'fr',
        licenseNumber: apiData.licenseNumber ?? '',
        professionalType: apiData.professionalType ?? 'CLINICIAN',
        specialty: apiData.specialty ?? '',
        signatureUrl: apiData.signatureUrl ?? '',
        avatarUrl: apiData.avatarUrl ?? '',
        avatarFile: null,
        address: {
            street: apiData.street ?? '',
            city: apiData.city ?? '',
            postalCode: apiData.postalCode ?? '',
            country: apiData.country ?? 'RDC',
        },
    };
}

export async function fetchProfessionals(): Promise<Professional[]> {
    try {
        const response = await apiClient.get<ApiFeedback<any[]>>('/professionals');
        const data = response.data.data ?? [];
        return data.map(mapApiToProfessional);
    } catch (error) {
        console.error('Erreur fetchProfessionals:', error);
        throw error;
    }
}

export async function getProfessionalById(id: string): Promise<ProfessionalFormValues> {
    try {
        const response = await apiClient.get<ApiFeedback<any>>(`/professionals/${id}`);
        if (response.data.error) {
            throw new Error(response.data.message || 'Professionnel introuvable');
        }
        return mapApiToProfessionalFormValues(response.data.data);
    } catch (error) {
        console.error('Erreur getProfessionalById:', error);
        throw error;
    }
}

export async function createProfessional(
    payload: ProfessionalFormValues,
    avatarFile?: File | null
): Promise<void> {
    const formData = buildFormData(payload, avatarFile);
    console.log('FormData envoyé:', [...formData.entries()]);

    try {
        const response = await apiClient.post<ApiFeedback<unknown>>('/professionals', formData, {
            headers: { 'Content-Type': undefined } as any,
        });
        if (response.data.error) {
            console.error('Réponse erreur création professionnel:', response.data);
            throw new Error(response.data.message || 'Erreur lors de la création du professionnel');
        }
    } catch (error) {
        console.error('Exception createProfessional:', error);
        if (error instanceof Error) throw error;
        throw new Error('Erreur inconnue lors de la création du professionnel');
    }
}

export async function updateProfessional(
    id: string,
    payload: ProfessionalFormValues,
    avatarFile?: File | null
): Promise<void> {
    const formData = buildFormData(payload, avatarFile);
    console.log('FormData envoyé (update):', [...formData.entries()]);

    try {
        const response = await apiClient.put<ApiFeedback<unknown>>(
            `/professionals/${id}`,
            formData,
            { headers: { 'Content-Type': undefined } as any }
        );
        if (response.data.error) {
            console.error('Réponse erreur mise à jour professionnel:', response.data);
            throw new Error(response.data.message || 'Erreur lors de la mise à jour du professionnel');
        }
    } catch (error) {
        console.error('Exception updateProfessional:', error);
        if (error instanceof Error) throw error;
        throw new Error('Erreur inconnue lors de la mise à jour du professionnel');
    }
}

export async function deleteProfessional(id: string): Promise<void> {
    try {
        const response = await apiClient.delete<ApiFeedback<unknown>>(`/professionals/${id}`);
        if (response.data.error) {
            console.error('Réponse erreur suppression professionnel:', response.data);
            throw new Error(response.data.message || 'Erreur lors de la suppression du professionnel');
        }
    } catch (error) {
        console.error('Exception deleteProfessional:', error);
        throw error;
    }
}

function buildFormData(payload: ProfessionalFormValues, avatarFile?: File | null): FormData {
    const formData = new FormData();

    // Champs scalaires
    const scalarFields: Record<string, any> = {
        email: payload.email,
        password: payload.password,
        fullName: payload.fullName,
        phone: payload.phone,
        gender: payload.gender,
        locale: payload.locale,
        licenseNumber: payload.licenseNumber,
        professionalType: payload.professionalType,
        specialty: payload.specialty,
        signatureUrl: payload.signatureUrl,
    };

    Object.entries(scalarFields).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            formData.append(key, String(value));
        }
    });

    // Adresse : envoi direct des champs
    if (payload.address) {
        const addressFields: Record<string, string | undefined> = {
            street: payload.address.street,
            city: payload.address.city,
            postalCode: payload.address.postalCode,
            country: payload.address.country,
        };

        Object.entries(addressFields).forEach(([key, value]) => {
            if (value) {
                formData.append(key, value);
            }
        });
    }

    // Fichier avatar
    if (avatarFile) {
        formData.append('avatarFile', avatarFile);
    }

    return formData;
}
