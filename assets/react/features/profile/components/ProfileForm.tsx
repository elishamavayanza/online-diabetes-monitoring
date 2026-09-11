import React, { useState, useRef } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Avatar } from '@/react/components/UI/Avatar';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { Select } from '@/react/components/Forms/Select';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { useI18n } from '@/react/i18n/I18nContext';
import { LOCALES } from '@/react/i18n/translations';
import { UserProfileData, ProfileUpdatePayload } from '../types';
import { changePassword } from '../services/profileService';

interface ProfileFormProps {
    profile: UserProfileData;
    onSave: (payload: ProfileUpdatePayload, avatarFile?: File | null) => void;
    isSaving: boolean;
}

export function ProfileForm({ profile, onSave, isSaving }: ProfileFormProps) {
    const { showToast } = useToast();
    const { t } = useI18n();
    const [name, setName] = useState(profile.name);
    const [phone, setPhone] = useState(profile.phone ?? '');
    const [locale, setLocale] = useState(profile.locale ?? 'fr');
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
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showToast({ type: 'error', message: t('Choisissez une image JPEG, PNG ou WebP.') });
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                showToast({ type: 'error', message: t('La photo de profil ne doit pas dépasser 2 Mo.') });
                return;
            }
            setAvatarFile(file);
            const reader = new FileReader();
            reader.onload = (e) => setAvatarPreview(e.target?.result as string);
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSave({ name, phone, avatarUrl: avatarPreview ?? undefined, locale }, avatarFile);
    };

    const handleChangePassword = async () => {
        if (!oldPassword || !newPassword || !confirmPassword) {
            showToast({ type: 'error', message: t('Veuillez remplir tous les champs de mot de passe.') });
            return;
        }
        if (newPassword !== confirmPassword) {
            showToast({ type: 'error', message: t('Les nouveaux mots de passe ne correspondent pas.') });
            return;
        }

        setIsChangingPassword(true);
        try {
            await changePassword({ oldPassword, newPassword, confirmPassword });
            showToast({ type: 'success', message: t('Mot de passe mis à jour avec succès.') });
            setOldPassword('');
            setNewPassword('');
            setConfirmPassword('');
        } catch (error) {
            const message = error instanceof Error ? error.message : t('Erreur lors du changement de mot de passe.');
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
                <FormField label={t('Nom complet')}>
                    <Input
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        required
                    />
                </FormField>
                <FormField label={t('Téléphone')}>
                    <Input
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                    />
                </FormField>
                <FormField label={t("Langue de l'interface")}>
                    <Select
                        value={locale}
                        fullWidth
                        onChange={(e) => setLocale(e.target.value)}
                        options={LOCALES.map((l) => ({ value: l.code, label: l.label }))}
                    />
                </FormField>
                <FormField label={t('Photo de profil')}>
                    <FileUpload
                        accept="image/*"
                        multiple={false}
                        maxFiles={1}
                        maxSizeInMB={5}
                        label={t('Cliquez ou déposez une nouvelle photo')}
                        hint={t('PNG, JPG recommandé')}
                        onFilesSelected={handleFileSelected}
                    />
                </FormField>

                <Button type="submit" disabled={isSaving}>
                    {isSaving ? t('Enregistrement...') : t('Enregistrer les modifications')}
                </Button>
            </Form>

            <div className="profile-card__password-section">
                <h3>{t('Changer le mot de passe')}</h3>
                <FormField label={t('Ancien mot de passe')}>
                    <Input
                        type="password"
                        value={oldPassword}
                        onChange={(e) => setOldPassword(e.target.value)}
                    />
                </FormField>
                <FormField label={t('Nouveau mot de passe')}>
                    <Input
                        type="password"
                        value={newPassword}
                        onChange={(e) => setNewPassword(e.target.value)}
                    />
                </FormField>
                <FormField label={t('Confirmer le nouveau mot de passe')}>
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
                    {isChangingPassword ? t('Mise à jour...') : t('Changer le mot de passe')}
                </Button>
            </div>
        </Card>
    );
}