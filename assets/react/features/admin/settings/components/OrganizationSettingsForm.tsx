import React, { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { OrganizationSettings, OrganizationStatus } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface OrganizationSettingsFormProps {
    settings: OrganizationSettings;
    onSave: (settings: OrganizationSettings) => void;
    isSaving: boolean;
}

export function OrganizationSettingsForm({ settings, onSave, isSaving }: OrganizationSettingsFormProps) {
    const { t } = useI18n();
    const [form, setForm] = useState<OrganizationSettings>(settings);

    const handleChange = (field: keyof OrganizationSettings, value: string | OrganizationStatus) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const typeOptions = [
        { value: 'Clinic', label: t('Clinique') },
        { value: 'Hospital', label: t('Hôpital') },
        { value: 'Network', label: t('Réseau') },
    ];

    const statutOptions = [
        { value: 'Active', label: t('Actif') },
        { value: 'Inactive', label: t('Inactif') },
    ];

    return (
        <Card className="org-settings-card">
            <h2>{t('Informations de l’organisation')}</h2>
            <Form onSubmit={(e: React.FormEvent) => { e.preventDefault(); onSave(form); }}>
                <FormField label={t('Nom court')}>
                    <Input
                        value={form.nomCourt}
                        onChange={(e) => handleChange('nomCourt', e.target.value)}
                    />
                </FormField>
                <FormField label={t('Type')}>
                    <Select
                        value={form.type}
                        onChange={(e) => handleChange('type', e.target.value)}
                        options={typeOptions}
                    />
                </FormField>
                <FormField label={t('Email')}>
                    <Input
                        type="email"
                        value={form.email}
                        onChange={(e) => handleChange('email', e.target.value)}
                    />
                </FormField>
                <FormField label={t('Téléphone')}>
                    <Input
                        value={form.telephone}
                        onChange={(e) => handleChange('telephone', e.target.value)}
                    />
                </FormField>
                <FormField label={t('Site web')}>
                    <Input
                        value={form.siteWeb}
                        onChange={(e) => handleChange('siteWeb', e.target.value)}
                    />
                </FormField>
                <FormField label={t('Adresse')}>
                    <Input
                        value={form.adresse}
                        onChange={(e) => handleChange('adresse', e.target.value)}
                    />
                </FormField>
                <FormField label={t('Statut')}>
                    <Select
                        value={form.statut}
                        onChange={(e) => handleChange('statut', e.target.value as OrganizationStatus)}
                        options={statutOptions}
                    />
                </FormField>
                <Button type="submit" disabled={isSaving}>
                    {isSaving ? t('Enregistrement...') : t('Enregistrer')}
                </Button>
            </Form>
        </Card>
    );
}
