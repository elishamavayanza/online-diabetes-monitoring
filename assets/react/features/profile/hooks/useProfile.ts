import { useEffect, useState } from 'react';
import { fetchUserProfile, updateUserProfile } from '../services/profileService';
import { UserProfileData, ProfileUpdatePayload } from '../types';

export function useProfile() {
    const [profile, setProfile] = useState<UserProfileData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchUserProfile();
                setProfile(data);
            } catch (err) {
                setError('Impossible de charger le profil.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    const save = async (payload: ProfileUpdatePayload) => {
        setIsSaving(true);
        setError(null);
        try {
            const updated = await updateUserProfile(payload);
            setProfile(updated);
        } catch (err) {
            setError('Erreur lors de la sauvegarde.');
        } finally {
            setIsSaving(false);
        }
    };

    return { profile, isLoading, isSaving, error, save };
}
