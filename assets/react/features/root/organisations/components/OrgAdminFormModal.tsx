import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateOrgAdmin } from '../hooks/useCreateOrgAdmin';
import { useI18n } from '@/react/i18n/I18nContext';

interface OrgAdminFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    organizationId: string;
    onSuccess?: () => void;
}

export function OrgAdminFormModal({ isOpen, onClose, organizationId, onSuccess }: OrgAdminFormModalProps) {
    const { t } = useI18n();
    const { form, updateField, updateAddress, submit, isSubmitting, error } = useCreateOrgAdmin(organizationId);

    const genderOptions = [
        { value: 'MALE', label: t('Masculin') },
        { value: 'FEMALE', label: t('F\u00e9minin') },
        { value: 'OTHER', label: t('Autre') },
        { value: 'UNSPECIFIED', label: t('Non sp\u00e9cifi\u00e9') },
    ];

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>{t('Ajouter un administrateur')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Nom complet *')}>
                        <Input
                            value={form.fullName}
                            onChange={(e) => updateField('fullName', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Email *')}>
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Mot de passe *')}>
                        <Input
                            type="password"
                            value={form.password}
                            onChange={(e) => updateField('password', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Genre *')}>
                        <Select
                            value={form.gender}
                            onChange={(e) => updateField('gender', e.target.value as any)}
                            options={genderOptions}
                        />
                    </FormField>
                    <FormField label={t('T\u00e9l\u00e9phone')}>
                        <Input
                            value={form.phone}
                            onChange={(e) => updateField('phone', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Locale">
                        <Input
                            value={form.locale}
                            onChange={(e) => updateField('locale', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Avatar URL">
                        <Input
                            value={form.avatarUrl}
                            onChange={(e) => updateField('avatarUrl', e.target.value)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__address">
                        <FormField label={t('Rue')}>
                            <Input value={form.address.street} onChange={(e) => updateAddress('street', e.target.value)} />
                        </FormField>
                        <FormField label={t('Ville')}>
                            <Input value={form.address.city} onChange={(e) => updateAddress('city', e.target.value)} />
                        </FormField>
                        <FormField label={t('Code postal')}>
                            <Input value={form.address.postalCode} onChange={(e) => updateAddress('postalCode', e.target.value)} />
                        </FormField>
                        <FormField label={t('Pays')}>
                            <Input value={form.address.country} onChange={(e) => updateAddress('country', e.target.value)} />
                        </FormField>
                    </div>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Cr\u00e9ation...') : t('Cr\u00e9er')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
