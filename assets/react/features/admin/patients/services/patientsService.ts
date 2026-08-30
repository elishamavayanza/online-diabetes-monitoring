// services/patientsService.ts
import apiClient from "@/services/api/client";
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';
import { Patient, PatientsFilters } from "@/react/features/admin/patients/types";
import { PatientFormValues } from "@/react/features/admin/patients/types/types";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

// Récupère la liste des patients
export async function fetchPatients(filters: PatientsFilters): Promise<Patient[]> {
    try {
        const response = await apiClient.get<ApiFeedback<any[]>>('/patients');
        const data = response.data.data ?? [];
        return data
            .map(mapApiToPatient)
            .filter((patient) => {
                const matchesSearch = patient.nom.toLowerCase().includes(filters.search.toLowerCase());
                const matchesType = filters.typeDiabete === 'Tous' || patient.typeDiabete === filters.typeDiabete;
                return matchesSearch && matchesType;
            });
    } catch (error) {
        console.error('Erreur fetchPatients:', error);
        throw error;
    }
}

// Récupère un patient complet (profil) par son ID
export async function getPatientById(id: string): Promise<PatientFormValues> {
    try {
        const response = await apiClient.get<ApiFeedback<any>>(`/patients/${id}/profile`);
        if (response.data.error) throw new Error(response.data.message || 'Patient introuvable');
        return mapApiToPatientFormValues(response.data.data);
    } catch (error) {
        console.error('Erreur getPatientById:', error);
        throw error;
    }
}

// Crée un patient (compte utilisateur + profil)
export async function createPatient(payload: PatientFormValues, avatarFile?: File | null): Promise<void> {
    //  Récupération explicite du token JWT
    const token = tokenStorage.getAccessToken();
    if (!token) {
        throw new Error("Token JWT manquant. Veuillez vous reconnecter.");
    }

    try {
        // 1. Créer le compte utilisateur (endpoint POST /api/users)
        const userPayload = {
            email: payload.email,
            password: payload.password,
            fullName: payload.fullName,
            phone: payload.phone,
            gender: payload.gender,
            locale: payload.locale,
            avatarUrl: payload.avatarUrl,
            address: payload.address,
        };
        const userResponse = await apiClient.post<ApiFeedback<any>>('/users', userPayload, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        if (userResponse.data.error) throw new Error(userResponse.data.message || 'Erreur lors de la création du compte patient');
        const userId = userResponse.data.data?.id;

        // 2. Mettre à jour le profil patient avec les informations médicales
        if (userId) {
            const profileFormData = buildPatientProfileFormData(payload, avatarFile);
            const profileResponse = await apiClient.put<ApiFeedback<unknown>>(
                `/patients/${userId}/profile`,
                profileFormData,
                {
                    headers: {
                        'Content-Type': undefined as any,
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            if (profileResponse.data.error) throw new Error(profileResponse.data.message || 'Erreur lors de la création du profil patient');
        }
    } catch (error) {
        console.error('Erreur createPatient:', error);
        throw error;
    }
}

// Met à jour le profil patient
export async function updatePatient(id: string, payload: PatientFormValues, avatarFile?: File | null): Promise<void> {
    const token = tokenStorage.getAccessToken();
    if (!token) {
        throw new Error("Token JWT manquant. Veuillez vous reconnecter.");
    }

    try {
        const formData = buildPatientProfileFormData(payload, avatarFile);
        const response = await apiClient.put<ApiFeedback<unknown>>(
            `/patients/${id}/profile`,
            formData,
            {
                headers: {
                    'Content-Type': undefined as any,
                    Authorization: `Bearer ${token}`,
                },
            }
        );
        if (response.data.error) throw new Error(response.data.message || 'Erreur lors de la mise à jour du patient');
    } catch (error) {
        console.error('Erreur updatePatient:', error);
        throw error;
    }
}

// --- Fonctions utilitaires ---

function mapApiToPatient(apiData: any): Patient {
    return {
        id: String(apiData.id ?? ''),
        nom: apiData.fullName ?? apiData.name ?? '',
        dateNaissance: apiData.dateOfBirth ?? '',
        typeDiabete: apiData.diabetesType ?? 'Type 1',
        equipeSoins: apiData.careTeamName ?? 'Non assigné',
        statut: apiData.active === false ? 'Inactive' : 'Active',
        avatarUrl: apiData.avatarUrl ?? '',
        email: apiData.email ?? '',
        telephone: apiData.phone ?? '',
    };
}

function mapApiToPatientFormValues(apiData: any): PatientFormValues {
    return {
        email: apiData.email ?? '',
        password: '', // laisser vide
        fullName: apiData.fullName ?? '',
        phone: apiData.phone ?? '',
        gender: apiData.gender ?? 'UNSPECIFIED',
        locale: apiData.locale ?? 'fr',
        avatarUrl: apiData.avatarUrl ?? '',
        avatarFile: null,
        address: {
            street: apiData.street ?? '',
            city: apiData.city ?? '',
            postalCode: apiData.postalCode ?? '',
            country: apiData.country ?? 'RDC',
        },
        dateOfBirth: apiData.dateOfBirth ?? '',
        placeOfBirth: apiData.placeOfBirth ?? '',
        bloodType: apiData.bloodType ?? '',
        heightCm: apiData.heightCm ? String(apiData.heightCm) : '',
    };
}

function buildPatientProfileFormData(payload: PatientFormValues, avatarFile?: File | null): FormData {
    const formData = new FormData();
    formData.append('fullName', payload.fullName);
    if (payload.phone) formData.append('phone', payload.phone);
    formData.append('gender', payload.gender);
    formData.append('locale', payload.locale);
    if (payload.dateOfBirth) formData.append('dateOfBirth', payload.dateOfBirth);
    if (payload.placeOfBirth) formData.append('placeOfBirth', payload.placeOfBirth);
    if (payload.bloodType) formData.append('bloodType', payload.bloodType);
    if (payload.heightCm) formData.append('heightCm', payload.heightCm);

    // Adresse
    if (payload.address) {
        formData.append('street', payload.address.street || '');
        formData.append('city', payload.address.city || '');
        formData.append('postalCode', payload.address.postalCode || '');
        formData.append('country', payload.address.country || 'RDC');
    }

    if (avatarFile) formData.append('avatarFile', avatarFile);
    return formData;
}
