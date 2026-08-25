import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { PatientsFilters } from '../types';

interface PatientsFilterProps {
    filters: PatientsFilters;
    onChange: (filters: PatientsFilters) => void;
}

export function PatientsFilter({ filters, onChange }: PatientsFilterProps) {
    const typeOptions = [
        { value: 'Tous', label: 'Tous' },
        { value: 'Type 1', label: 'Type 1' },
        { value: 'Type 2', label: 'Type 2' },
        { value: 'Gestationnel', label: 'Gestationnel' },
    ];

    return (
        <div className="patients-filter">
            <FormField label="Rechercher">
                <Input
                    placeholder="Nom du patient..."
                    value={filters.search}
                    onChange={(e) => onChange({ ...filters, search: e.target.value })}
                />
            </FormField>
            <FormField label="Type de diabète">
                <Select
                    value={filters.typeDiabete}
                    onChange={(e) => onChange({ ...filters, typeDiabete: e.target.value as typeof filters.typeDiabete })}
                    options={typeOptions}
                />
            </FormField>
        </div>
    );
}
