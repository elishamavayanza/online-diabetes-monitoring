import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { useI18n } from '@/react/i18n/I18nContext';

interface AddressFieldsProps {
    address: {
        street: string;
        city: string;
        postalCode: string;
        country: string;
    };
    onChange: (field: 'street' | 'city' | 'postalCode' | 'country', value: string) => void;
}

export function AddressFields({ address, onChange }: AddressFieldsProps) {
    const { t } = useI18n();
    return (
        <div className="address-grid">
            <FormField label={t('Rue')}>
                <Input value={address.street} onChange={(e) => onChange('street', e.target.value)} />
            </FormField>
            <FormField label={t('Ville')}>
                <Input value={address.city} onChange={(e) => onChange('city', e.target.value)} />
            </FormField>
            <FormField label={t('Code postal')}>
                <Input value={address.postalCode} onChange={(e) => onChange('postalCode', e.target.value)} />
            </FormField>
            <FormField label={t('Pays')}>
                <Input value={address.country} onChange={(e) => onChange('country', e.target.value)} />
            </FormField>
        </div>
    );
}
