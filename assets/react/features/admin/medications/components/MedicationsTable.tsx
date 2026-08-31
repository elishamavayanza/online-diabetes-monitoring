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

// Mapping des catégories backend vers des libellés français
const CATEGORY_LABELS: Record<string, string> = {
    INSULIN: 'Insuline',
    TABLET: 'Comprimé',
    OTHER: 'Autre',
};

export function MedicationsTable({ medications, onEdit, onDelete }: MedicationsTableProps) {
    const columns = [
        { key: 'name', title: 'Nom' },
        {
            key: 'category',
            title: 'Catégorie',
            render: (row: Medication) => CATEGORY_LABELS[row.category] ?? row.category,
        },
        {
            key: 'manufacturer',
            title: 'Fabricant',
            render: (row: Medication) => row.manufacturer ?? '—',
        },
        {
            key: 'insulinLevel',
            title: 'Niveau d’insuline',
            render: (row: Medication) => row.insulinLevel !== undefined ? row.insulinLevel : '—',
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
