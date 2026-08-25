import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Member } from '../types';

interface MembersTableProps {
    members: Member[];
}

export function MembersTable({ members }: MembersTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'role', title: 'Rôle' },
        { key: 'etablissement', title: 'Établissement', render: (row: Member) => row.etablissement ?? '—' },
        { key: 'departement', title: 'Département', render: (row: Member) => row.departement ?? '—' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Member) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        { key: 'dateArrivee', title: 'Date d\'arrivée' },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Member) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="members-card">
            <DataTable columns={columns} data={members} />
        </Card>
    );
}
