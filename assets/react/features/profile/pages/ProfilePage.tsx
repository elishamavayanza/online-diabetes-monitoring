import { useProfile } from '../hooks/useProfile';
import { ProfileForm } from '../components/ProfileForm';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/profile/_profile.scss';

export function ProfilePage() {
    const { profile, isLoading, isSaving, error, save } = useProfile();

    if (isLoading) return <Spinner />;
    if (error || !profile) return <Alert variant="error">{error ?? 'Profil indisponible'}</Alert>;

    return (
        <div className="profile-page">
            <div className="profile-page__header">
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles</p>
            </div>
            <ProfileForm profile={profile} onSave={save} isSaving={isSaving} />
        </div>
    );
}
