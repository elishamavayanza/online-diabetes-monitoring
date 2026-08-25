import React, { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { SettingsData } from '../types';

interface GeneralSettingsProps {
    settings: SettingsData;
    onSave: (settings: SettingsData) => void;
    isSaving: boolean;
}

export function GeneralSettings({ settings, onSave, isSaving }: GeneralSettingsProps) {
    const [form, setForm] = useState<SettingsData>(settings);

    const handleChange = (field: keyof SettingsData, value: unknown) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    return (
        <Card className="settings-card">
            <h2>Configuration générale</h2>
            <Form
                onSubmit={(e: React.FormEvent) => {
                    e.preventDefault();
                    onSave(form);
                }}
            >
                <FormField label="Nom de la plateforme">
                    <Input
                        value={form.platformName}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            handleChange('platformName', e.target.value)
                        }
                    />
                </FormField>

                <FormField label="Mode maintenance">
                    <Switch
                        checked={form.maintenanceMode}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            handleChange('maintenanceMode', e.target.checked)
                        }
                    />
                </FormField>

                <FormField label="Autoriser l’inscription">
                    <Switch
                        checked={form.allowRegistration}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            handleChange('allowRegistration', e.target.checked)
                        }
                    />
                </FormField>

                <FormField label="Langue par défaut">
                    <Input
                        value={form.defaultLanguage}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                            handleChange('defaultLanguage', e.target.value)
                        }
                    />
                </FormField>

                <Button type="submit" disabled={isSaving}>
                    {isSaving ? 'Enregistrement...' : 'Enregistrer'}
                </Button>
            </Form>
        </Card>
    );
}
