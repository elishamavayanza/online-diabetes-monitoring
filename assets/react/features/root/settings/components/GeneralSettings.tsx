import React from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { SettingsData } from '../types';

interface GeneralSettingsProps {
    settings: SettingsData;
    logoFile: File | null;
    onChange: (patch: Partial<SettingsData>) => void;
    onLogoChange: (file: File | null) => void;
    onSave: () => void;
    isSaving: boolean;
}

export function GeneralSettings({
    settings,
    logoFile,
    onChange,
    onLogoChange,
    onSave,
    isSaving,
}: GeneralSettingsProps) {
    return (
        <Card className="settings-card">
            <h2>Identité de la plateforme</h2>
            <Form
                onSubmit={(e: React.FormEvent) => {
                    e.preventDefault();
                    onSave();
                }}
            >
                <FormField label="Nom du système">
                    <Input
                        value={settings.systemName}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            onChange({ systemName: e.target.value })
                        }
                    />
                </FormField>

                <FormField
                    label="Logo"
                    helpText="PNG, JPEG ou WebP (2 Mo max). Utilisé dans la sidebar, la page de connexion et le pied de page."
                >
                    {settings.logoUrl && (
                        <img
                            src={logoFile ? URL.createObjectURL(logoFile) : settings.logoUrl}
                            alt="Aperçu du logo"
                            className="settings-logo__preview"
                        />
                    )}
                    <FileUpload
                        accept="image/jpeg,image/png,image/webp"
                        maxFiles={1}
                        maxSizeInMB={2}
                        onFilesSelected={(files) => onLogoChange(files[0] ?? null)}
                        label="Cliquez ou déposez un logo ici"
                    />
                </FormField>

                <Button type="submit" disabled={isSaving}>
                    {isSaving ? 'Enregistrement...' : 'Enregistrer'}
                </Button>
            </Form>
        </Card>
    );
}