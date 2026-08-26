import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateOrganisation } from '../hooks/useUpdateOrganisation';
import { CreateOrganisationPayload } from '../types';

interface OrganisationEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    organisationData: CreateOrganisationPayload;
}

export function OrganisationEditModal({ isOpen, onClose, organisationData }: OrganisationEditModalProps) {
    const { form, updateField, updateAddress, submit, isSubmitting, error } = useUpdateOrganisation(organisationData);

    const typeOptions = [
        { value: 'HOSPITAL', label: 'Hôpital' },
        { value: 'CLINIC', label: 'Clinique' },
        { value: 'NETWORK', label: 'Réseau' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>Modifier l'organisation</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Nom complet *">
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Nom court">
                        <Input
                            value={form.shortName}
                            onChange={(e) => updateField('shortName', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Type *">
                        <Select
                            value={form.type}
                            onChange={(e) => updateField('type', e.target.value as any)}
                            options={typeOptions}
                        />
                    </FormField>
                    <FormField label="Email">
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Téléphone">
                        <Input
                            value={form.phone}
                            onChange={(e) => updateField('phone', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Site Web">
                        <Input
                            value={form.website}
                            onChange={(e) => updateField('website', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Logo URL">
                        <Input
                            value={form.logoUrl}
                            onChange={(e) => updateField('logoUrl', e.target.value)}
                        />
                    </FormField>

                    {/* Adresse */}
                    <div className="organisation-form-modal__address">
                        <FormField label="Rue">
                            <Input value={form.address?.street ?? ''} onChange={(e) => updateAddress('street', e.target.value)} />
                        </FormField>
                        <FormField label="Ville">
                            <Input value={form.address?.city ?? ''} onChange={(e) => updateAddress('city', e.target.value)} />
                        </FormField>
                        <FormField label="Code postal">
                            <Input value={form.address?.postalCode ?? ''} onChange={(e) => updateAddress('postalCode', e.target.value)} />
                        </FormField>
                        <FormField label="Pays">
                            <Input value={form.address?.country ?? ''} onChange={(e) => updateAddress('country', e.target.value)} />
                        </FormField>
                    </div>

                    <FormField label="Actif">
                        <Switch
                            checked={form.active}
                            onChange={(e) => updateField('active', e.target.checked)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Mise à jour...' : 'Enregistrer'}</Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
