import { useI18n } from '@/react/i18n/I18nContext';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { PatientsFilters } from '../types';

interface PatientsFilterProps {
    filters: PatientsFilters;
    onChange: (filters: PatientsFilters) => void;
}

export function PatientsFilter({ filters, onChange }: PatientsFilterProps) {
    const { t } = useI18n();

    const typeOptions = [
        { value: 'Tous', label: t('Tous') },
        { value: 'Type 1', label: t('Type 1') },
        { value: 'Type 2', label: t('Type 2') },
        { value: 'Gestationnel', label: t('Gestationnel') },
    ];

    return (
        <div className="patients-filter">
            <FormField label={t('Rechercher')}>
                <Input
                    placeholder={t('Nom du patient...')}
                    value={filters.search}
                    onChange={(e) => onChange({ ...filters, search: e.target.value })}
                />
            </FormField>
            <FormField label={t('Type de diabète')}>
                <Select
                    value={filters.typeDiabete}
                    onChange={(e) => onChange({ ...filters, typeDiabete: e.target.value as typeof filters.typeDiabete })}
                    options={typeOptions}
                />
            </FormField>
        </div>
    );
}
