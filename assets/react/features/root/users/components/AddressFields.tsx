import React from 'react';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';

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
    return (
        <div className="address-grid">
            <FormField label="Rue">
                <Input value={address.street} onChange={(e) => onChange('street', e.target.value)} />
            </FormField>
            <FormField label="Ville">
                <Input value={address.city} onChange={(e) => onChange('city', e.target.value)} />
            </FormField>
            <FormField label="Code postal">
                <Input value={address.postalCode} onChange={(e) => onChange('postalCode', e.target.value)} />
            </FormField>
            <FormField label="Pays">
                <Input value={address.country} onChange={(e) => onChange('country', e.target.value)} />
            </FormField>
        </div>
    );
}
