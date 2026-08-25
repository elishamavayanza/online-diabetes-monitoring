import { UserProfileData, ProfileUpdatePayload } from '../types';

export async function fetchUserProfile(): Promise<UserProfileData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Simulation des données utilisateur (à remplacer par un appel API)
    return {
        id: 'user-123',
        name: 'Jean Dupont',
        email: 'jean.dupont@diabcare.com',
        role: 'ROOT',
        phone: '+243 990 111 222',
        avatarUrl: 'https://via.placeholder.com/150',
        locale: 'fr',
    };
}

export async function updateUserProfile(payload: ProfileUpdatePayload): Promise<UserProfileData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Simulation de mise à jour
    console.log('Profil mis à jour', payload);

    return {
        id: 'user-123',
        name: payload.name,
        email: 'jean.dupont@diabcare.com',
        role: 'ROOT',
        phone: payload.phone,
        avatarUrl: payload.avatarUrl,
        locale: 'fr',
    };
}
