import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Medication } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

interface MedicationsTableProps {
    medications: Medication[];
    onEdit: (medication: Medication) => void;
    onDelete: (medication: Medication) => void;
}

const CATEGORY_LABELS: Record<string, string> = {
    INSULIN: 'Insuline',
    GENERAL: 'Général',
};

const FORM_LABELS: Record<string, string> = {
    TABLET: 'Comprimé',
    LIQUID: 'Liquide',
};

const INSULIN_TYPE_LABELS: Record<string, string> = {
    RAPID_ACTING: 'Action rapide',
    SHORT_ACTING: 'Action courte',
    INTERMEDIATE_ACTING: 'Action intermédiaire',
    LONG_ACTING: 'Action longue',
    MIXED: 'Prémélangée',
    OTHER: 'Autre',
};

function medicationCharacteristics(medication: Medication): string {
    if (medication.category === 'GENERAL') {
        return FORM_LABELS[medication.form ?? ''] ?? 'Non renseignée';
    }

    const insulinType = INSULIN_TYPE_LABELS[medication.insulinType ?? '']
        ?? medication.insulinType
        ?? 'Type non renseigné';

    return medication.concentration
        ? `${insulinType} (${medication.concentration})`
        : insulinType;
}

export function MedicationsTable({ medications, onEdit, onDelete }: MedicationsTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'name', title: t('Nom') },
        {
            key: 'category',
            title: t('Classe'),
            render: (row: Medication) => (
                <Badge variant={row.category === 'INSULIN' ? 'info' : 'secondary'}>
                    {t(CATEGORY_LABELS[row.category] ?? row.category)}
                </Badge>
            ),
        },
        {
            key: 'characteristics',
            title: t('Caractéristiques'),
            render: (row: Medication) => medicationCharacteristics(row),
        },
        {
            key: 'manufacturer',
            title: t('Fabricant'),
            render: (row: Medication) => row.manufacturer ?? '—',
        },
        {
            key: 'active',
            title: t('Statut'),
            render: (row: Medication) => (
                <Badge variant={row.active ? 'success' : 'error'}>
                    {row.active ? t('Actif') : t('Inactif')}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Medication) => (
                <div style={{ display: 'flex', gap: '0.5rem' }}>
                    <Button variant="secondary" size="small" onClick={() => onEdit(row)}>{t('Modifier')}</Button>
                    <Button variant="danger" size="small" onClick={() => onDelete(row)}>{t('Supprimer')}</Button>
                </div>
            ),
        },
    ];

    return (
        <Card className="medications-card">
            <DataTable columns={columns} data={medications} pageSize={10} />
        </Card>
    );
}
