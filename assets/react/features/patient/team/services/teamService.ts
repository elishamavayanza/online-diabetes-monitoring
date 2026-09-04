// services/teamService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { CareTeamMember } from '../types';

// Fonction pour construire l'URL complète de la photo (si relative)
const buildPhotoUrl = (photoUrl?: string): string | undefined => {
    if (!photoUrl) return undefined;
    if (photoUrl.startsWith('http')) return photoUrl;
    const baseUrl = (import.meta as unknown as { env: { VITE_API_BASE_URL?: string } }).env.VITE_API_BASE_URL || '';
    return `${baseUrl}${photoUrl}`;
};

export async function fetchCareTeam(patientId: string): Promise<CareTeamMember[]> {
    const response = await apiClient.get<ApiFeedback<any[]>>(`/patients/${patientId}/team`);
    const teamData = unwrapApiData(response.data, 'Erreur lors du chargement de l\'équipe.');

    return teamData.map((member: any) => ({
        id: member.id?.toString() ?? '',
        nom: member.fullName ?? member.name ?? '',
        role: mapRole(member.roles ?? []),
        specialite: member.specialty ?? member.specialite,
        fonction: member.fonction ?? member.jobTitle ?? '',
        photoUrl: buildPhotoUrl(member.photoUrl ?? member.avatar ?? member.photo),
    }));
}

function mapRole(roles: string[]): CareTeamMember['role'] {
    if (!Array.isArray(roles) || roles.length === 0) return 'Autre';

    const role = roles[0].toUpperCase();

    if (role === 'ROLE_CLINICIAN') {
        return 'Clinician';
    }
    if (role === 'ROLE_NUTRITIONIST') {
        return 'Nutritionniste';
    }
    return 'Autre';
}
