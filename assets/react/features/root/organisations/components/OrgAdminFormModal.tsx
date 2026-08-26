import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateOrgAdmin } from '../hooks/useCreateOrgAdmin';

interface OrgAdminFormModalProps {
    isOpen: boolean;
    onClose: () => void;
}

export function OrgAdminFormModal({ isOpen, onClose }: OrgAdminFormModalProps) {
    const { form, updateField, updateAddress, submit, isSubmitting, error } = useCreateOrgAdmin();

    const genderOptions = [
        { value: 'MALE', label: 'Masculin' },
        { value: 'FEMALE', label: 'Féminin' },
        { value: 'OTHER', label: 'Autre' },
        { value: 'UNSPECIFIED', label: 'Non spécifié' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>Ajouter un administrateur</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Nom complet *">
                        <Input
                            value={form.fullName}
                            onChange={(e) => updateField('fullName', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Email *">
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Mot de passe *">
                        <Input
                            type="password"
                            value={form.password}
                            onChange={(e) => updateField('password', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label="Genre *">
                        <Select
                            value={form.gender}
                            onChange={(e) => updateField('gender', e.target.value as any)}
                            options={genderOptions}
                        />
                    </FormField>
                    <FormField label="Téléphone">
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

                    {/* Adresse */}
                    <div className="organisation-form-modal__address">
                        <FormField label="Rue">
                            <Input value={form.address.street} onChange={(e) => updateAddress('street', e.target.value)} />
                        </FormField>
                        <FormField label="Ville">
                            <Input value={form.address.city} onChange={(e) => updateAddress('city', e.target.value)} />
                        </FormField>
                        <FormField label="Code postal">
                            <Input value={form.address.postalCode} onChange={(e) => updateAddress('postalCode', e.target.value)} />
                        </FormField>
                        <FormField label="Pays">
                            <Input value={form.address.country} onChange={(e) => updateAddress('country', e.target.value)} />
                        </FormField>
                    </div>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Création...' : 'Créer'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
