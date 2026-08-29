import { useEffect, useState } from 'react';
import { fetchUserProfile, updateUserProfile } from '../services/profileService';
import { UserProfileData, ProfileUpdatePayload } from '../types';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useProfile() {
    const { user } = useAuth();
    const { showToast } = useToast();
    const [profile, setProfile] = useState<UserProfileData | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchUserProfile(user?.id); // ✅ passe l'ID utilisateur
                setProfile(data);
            } catch (err) {
                const message = err instanceof Error ? err.message : 'Impossible de charger le profil.';
                setError(message);
                showToast({ type: 'error', message });
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [user?.id, showToast]);

    const save = async (payload: ProfileUpdatePayload, avatarFile?: File | null) => {
        setIsSaving(true);
        setError(null);
        try {
            const updated = await updateUserProfile(payload, avatarFile, user?.id); // passe l'ID
            setProfile(updated);
            showToast({ type: 'success', message: 'Profil mis à jour avec succès.' });
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la sauvegarde.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsSaving(false);
        }
    };

    return { profile, isLoading, isSaving, error, save };
}
