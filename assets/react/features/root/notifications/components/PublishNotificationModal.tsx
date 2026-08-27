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
import { CreateSystemNotificationPayload } from '../types';

interface PublishNotificationModalProps {
    isOpen: boolean;
    onClose: () => void;
}

export function PublishNotificationModal({ isOpen, onClose }: PublishNotificationModalProps) {
    const { form, updateField, submit, isSubmitting, error } = usePublishSystemNotification();

    const scopeOptions = [
        { value: 'GLOBAL', label: 'Tous les utilisateurs' },
        { value: 'ORGANIZATION', label: 'Une organisation' },
        { value: 'USER', label: 'Un utilisateur' },
    ];

    const channelOptions = [
        { value: 'IN_APP', label: 'In-app' },
        { value: 'PUSH', label: 'Push' },
        { value: 'EMAIL', label: 'Email' },
        { value: 'SMS', label: 'SMS' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="publish-notification-modal">
                <h2>Publier une notification système</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Portée *">
                        <Select
                            value={form.scope}
                            onChange={(e) => updateField('scope', e.target.value as CreateSystemNotificationPayload['scope'])}
                            options={scopeOptions}
                        />
                    </FormField>

                    {form.scope === 'USER' && (
                        <FormField label="Rechercher un utilisateur (par email) *">
                            <SearchInput
                                placeholder="Entrez l'email de l'utilisateur..."
                                value={form.userId ?? ''}
                                onSearch={(value) => updateField('userId', value)}
                                inputProps={{ required: true }}   // ✅ required passé via inputProps
                            />
                        </FormField>
                    )}
                    {form.scope === 'ORGANIZATION' && (
                        <FormField label="ID Organisation *">
                            <Input
                                value={form.organizationId ?? ''}
                                onChange={(e) => updateField('organizationId', e.target.value)}
                                required
                            />
                        </FormField>
                    )}

                    <FormField label="Type *">
                        <Select
                            value={form.type}
                            onChange={(e) => updateField('type', e.target.value as CreateSystemNotificationPayload['type'])}
                            options={[
                                { value: 'SYSTEM_ALERT', label: 'Alerte système' },
                            ]}
                        />
                    </FormField>

                    <FormField label="Canal *">
                        <Select
                            value={form.channel}
                            onChange={(e) => updateField('channel', e.target.value as CreateSystemNotificationPayload['channel'])}
                            options={channelOptions}
                        />
                    </FormField>

                    <FormField label="Titre *">
                        <Input value={form.title} onChange={(e) => updateField('title', e.target.value)} required />
                    </FormField>

                    <FormField label="Message *">
                        <Textarea
                            value={form.body}
                            onChange={(e) => updateField('body', e.target.value)}
                            required
                        />
                    </FormField>

                    <div className="publish-notification-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Publication...' : 'Publier'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
