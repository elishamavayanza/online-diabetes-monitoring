import React, { useState, useRef } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Avatar } from '@/react/components/UI/Avatar';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { UserProfileData, ProfileUpdatePayload } from '../types';
import { changePassword } from '../services/profileService'; // à créer

interface ProfileFormProps {
    profile: UserProfileData;
    onSave: (payload: ProfileUpdatePayload, avatarFile?: File | null) => void;
    isSaving: boolean;
}

export function ProfileForm({ profile, onSave, isSaving }: ProfileFormProps) {
    const { showToast } = useToast();
    const [name, setName] = useState(profile.name);
    const [phone, setPhone] = useState(profile.phone ?? '');
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(profile.avatarUrl ?? null);

    // État pour le changement de mot de passe
    const [oldPassword, setOldPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [isChangingPassword, setIsChangingPassword] = useState(false);

    const handleFileSelected = (files: File[]) => {
        if (files.length > 0) {
            const file = files[0];
            setAvatarFile(file);
            const reader = new FileReader();
            reader.onload = (e) => setAvatarPreview(e.target?.result as string);
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSave({ name, phone, avatarUrl: avatarPreview ?? undefined }, avatarFile);
    };

    const handleChangePassword = async () => {
        if (!oldPassword || !newPassword || !confirmPassword) {
            showToast({ type: 'error', message: 'Veuillez remplir tous les champs de mot de passe.' });
            return;
        }
        if (newPassword !== confirmPassword) {
            showToast({ type: 'error', message: 'Les nouveaux mots de passe ne correspondent pas.' });
            return;
        }

        setIsChangingPassword(true);
        try {
            await changePassword({ oldPassword, newPassword, confirmPassword });
            showToast({ type: 'success', message: 'Mot de passe mis à jour avec succès.' });
            setOldPassword('');
            setNewPassword('');
            setConfirmPassword('');
        } catch (error) {
            const message = error instanceof Error ? error.message : 'Erreur lors du changement de mot de passe.';
            showToast({ type: 'error', message });
        } finally {
            setIsChangingPassword(false);
        }
    };

    return (
        <Card className="profile-card">
            <div className="profile-card__header">
                <Avatar
                    src={avatarPreview ?? undefined}
                    name={name}
                    size="large"
                    shape="circle"
                />
                <div>
                    <h2>{name}</h2>
                    <p>{profile.email} • {profile.role}</p>
                </div>
            </div>

            <Form onSubmit={handleSubmit}>
                <FormField label="Nom complet">
                    <Input
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        required
                    />
                </FormField>
                <FormField label="Téléphone">
                    <Input
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                    />
                </FormField>
                <FormField label="Photo de profil">
                    <FileUpload
                        accept="image/*"
                        multiple={false}
                        maxFiles={1}
                        maxSizeInMB={5}
                        label="Cliquez ou déposez une nouvelle photo"
                        hint="PNG, JPG recommandé"
                        onFilesSelected={handleFileSelected}
                    />
                </FormField>

                <Button type="submit" disabled={isSaving}>
                    {isSaving ? 'Enregistrement...' : 'Enregistrer les modifications'}
                </Button>
            </Form>

            <div className="profile-card__password-section">
                <h3>Changer le mot de passe</h3>
                <FormField label="Ancien mot de passe">
                    <Input
                        type="password"
                        value={oldPassword}
                        onChange={(e) => setOldPassword(e.target.value)}
                    />
                </FormField>
                <FormField label="Nouveau mot de passe">
                    <Input
                        type="password"
                        value={newPassword}
                        onChange={(e) => setNewPassword(e.target.value)}
                    />
                </FormField>
                <FormField label="Confirmer le nouveau mot de passe">
                    <Input
                        type="password"
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                    />
                </FormField>
                <Button
                    variant="secondary"
                    onClick={handleChangePassword}
                    disabled={isChangingPassword}
                >
                    {isChangingPassword ? 'Mise à jour...' : 'Changer le mot de passe'}
                </Button>
            </div>
        </Card>
    );
}
