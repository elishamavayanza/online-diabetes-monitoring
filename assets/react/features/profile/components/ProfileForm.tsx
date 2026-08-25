import React, { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Avatar } from '@/react/components/UI/Avatar';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { UserProfileData, ProfileUpdatePayload } from '../types';

interface ProfileFormProps {
    profile: UserProfileData;
    onSave: (payload: ProfileUpdatePayload) => void;
    isSaving: boolean;
}

export function ProfileForm({ profile, onSave, isSaving }: ProfileFormProps) {
    const [name, setName] = useState(profile.name);
    const [phone, setPhone] = useState(profile.phone ?? '');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSave({ name, phone, avatarUrl: profile.avatarUrl });
    };

    return (
        <Card className="profile-card">
            <div className="profile-card__header">
                <Avatar
                    src={profile.avatarUrl}
                    name={profile.name}
                    size="large"
                    shape="circle"
                />
                <div>
                    <h2>{profile.name}</h2>
                    <p>{profile.email} • {profile.role}</p>
                </div>
            </div>

            <Form onSubmit={handleSubmit}>
                <FormField label="Nom complet">
                    <Input
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                    />
                </FormField>
                <FormField label="Téléphone">
                    <Input
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                    />
                </FormField>
                {/* Ajoutez ici d'autres champs si nécessaire (locale, etc.) */}
                <Button type="submit" disabled={isSaving}>
                    {isSaving ? 'Enregistrement...' : 'Enregistrer'}
                </Button>
            </Form>
        </Card>
    );
}
