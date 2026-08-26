import { useState } from 'react';
import { useProfile } from '../hooks/useProfile';
import { ProfileForm } from '../components/ProfileForm';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/profile/_profile.scss';

export function ProfilePage() {
    const { profile, isLoading, isSaving, error, save } = useProfile();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        // Action inverse : fermer la modale
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !profile) return <Alert variant="error">{error ?? 'Profil indisponible'}</Alert>;

    return (
        <div className="profile-page">
            <div className="profile-page__header">
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <ProfileForm profile={profile} onSave={save} isSaving={isSaving} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Vous pouvez modifier votre nom et votre téléphone, puis enregistrer vos changements.</p>
                </Modal>
            )}
        </div>
    );
}
