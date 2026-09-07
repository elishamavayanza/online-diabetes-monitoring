import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Medication } from '../types/types';

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

export function MedicationsTable({ medications, onEdit, onDelete }: MedicationsTableProps) {
    const columns = [
        { key: 'name', title: 'Nom' },
        {
            key: 'category',
            title: 'Classe',
            render: (row: Medication) => (
                <Badge variant={row.category === 'INSULIN' ? 'info' : 'secondary'}>
                    {CATEGORY_LABELS[row.category] ?? row.category}
                </Badge>
            ),
        },
        {
            key: 'form',
            title: 'Forme',
            render: (row: Medication) =>
                row.category === 'GENERAL' ? (FORM_LABELS[row.form ?? ''] ?? '—') : '—',
        },
        {
            key: 'insulin',
            title: 'Type / Concentration',
            render: (row: Medication) =>
                row.category === 'INSULIN' ? (
                    <>
                        {INSULIN_TYPE_LABELS[row.insulinType ?? ''] ?? row.insulinType ?? '—'}
                        {row.concentration ? ` (${row.concentration})` : ''}
                    </>
                ) : (
                    '—'
                ),
        },
        {
            key: 'manufacturer',
            title: 'Fabricant',
            render: (row: Medication) => row.manufacturer ?? '—',
        },
        {
            key: 'active',
            title: 'Statut',
            render: (row: Medication) => (
                <Badge variant={row.active ? 'success' : 'error'}>
                    {row.active ? 'Actif' : 'Inactif'}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Medication) => (
                <div style={{ display: 'flex', gap: '0.5rem' }}>
                    <Button variant="secondary" size="small" onClick={() => onEdit(row)}>Modifier</Button>
                    <Button variant="danger" size="small" onClick={() => onDelete(row)}>Supprimer</Button>
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