import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateEstablishment } from '../hooks/useUpdateEstablishment';
import { Establishment } from '../types/establishment';
import { useI18n } from '@/react/i18n/I18nContext';

interface EstablishmentEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    establishment: Establishment;
}

export function EstablishmentEditModal({ isOpen, onClose, establishment }: EstablishmentEditModalProps) {
    const { t } = useI18n();
    const { form, updateField, updateAddress, submit, isSubmitting, error } = useUpdateEstablishment(establishment);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>{t("Modifier l\u2019établissement")}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t("Nom de l\u2019établissement *")}>
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Téléphone')}>
                        <Input
                            value={form.phone}
                            onChange={(e) => updateField('phone', e.target.value)}
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
                            {isSubmitting ? t('Mise à jour...') : t('Enregistrer')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
