import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Textarea } from '@/react/components/Forms/Textarea';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { usePublishSystemNotification } from '../hooks/usePublishSystemNotification';
import { CreateSystemNotificationPayload, PUBLICATION_ROLES } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface PublishNotificationModalProps {
    isOpen: boolean;
    onClose: () => void;
    onPublished?: () => void;
}

export function PublishNotificationModal({ isOpen, onClose, onPublished }: PublishNotificationModalProps) {
    const { t } = useI18n();
    const { form, updateField, submit, isSubmitting, error } = usePublishSystemNotification();

    const scopeOptions = [
        { value: 'GLOBAL', label: t('Tous les utilisateurs') },
        { value: 'ROLE', label: t('Par niveau (rôle)') },
        { value: 'ORGANIZATION', label: t('Une organisation') },
        { value: 'USER', label: t('Un utilisateur') },
    ];

    const channelOptions = [
        { value: 'IN_APP', label: 'In-app' },
        { value: 'EMAIL', label: 'Email (Mailpit)' },
        { value: 'PUSH', label: 'Push' },
        { value: 'SMS', label: 'SMS' },
    ];

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onClose();
            onPublished?.();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="publish-notification-modal">
                <h2>{t('Publier une notification système')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Niveau de publication *')}>
                        <Select
                            value={form.scope}
                            onChange={(e) => updateField('scope', e.target.value as CreateSystemNotificationPayload['scope'])}
                            options={scopeOptions}
                        />
                    </FormField>

                    {form.scope === 'ROLE' && (
                        <FormField label={t('Rôle cible *')}>
                            <Select
                                value={form.role ?? 'ROLE_PATIENT'}
                                onChange={(e) => updateField('role', e.target.value)}
                                options={PUBLICATION_ROLES.map((r) => ({ value: r.value, label: r.label }))}
                            />
                        </FormField>
                    )}

                    {form.scope === 'USER' && (
                        <FormField label={t('Rechercher un utilisateur (par email) *')}>
                            <SearchInput
                                placeholder={t('Entrez l\u2019email de l\u2019utilisateur...')}
                                value={form.userId ?? ''}
                                onSearch={(value) => updateField('userId', value)}
                                inputProps={{ required: true }}
                            />
                        </FormField>
                    )}
                    {form.scope === 'ORGANIZATION' && (
                        <FormField label={t('ID Organisation *')}>
                            <Input
                                value={form.organizationId ?? ''}
                                onChange={(e) => updateField('organizationId', e.target.value)}
                                required
                            />
                        </FormField>
                    )}

                    <FormField label={t('Type *')}>
                        <Select
                            value={form.type}
                            onChange={(e) => updateField('type', e.target.value as CreateSystemNotificationPayload['type'])}
                            options={[
                                { value: 'SYSTEM_ALERT', label: t('Alerte système') },
                            ]}
                        />
                    </FormField>

                    <FormField label={t('Canal *')}>
                        <Select
                            value={form.channel}
                            onChange={(e) => updateField('channel', e.target.value as CreateSystemNotificationPayload['channel'])}
                            options={channelOptions}
                        />
                    </FormField>

                    <FormField label={t('Titre *')}>
                        <Input value={form.title} onChange={(e) => updateField('title', e.target.value)} required />
                    </FormField>

                    <FormField label={t('Message *')}>
                        <Textarea
                            value={form.body}
                            onChange={(e) => updateField('body', e.target.value)}
                            required
                        />
                    </FormField>

                    <div className="publish-notification-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Publication...') : t('Publier')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
