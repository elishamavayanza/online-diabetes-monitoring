import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { User } from '../types';

interface UsersTableProps {
    users: User[];
    onViewDetails: (user: User) => void;
}

export function UsersTable({ users, onViewDetails }: UsersTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'email', title: 'Email' },
        { key: 'type', title: 'Type' },
        {
            key: 'organisation',
            title: 'Organisation',
            render: (row: User) =>
                row.organisation ? (
                    <Badge variant="success">{row.organisation}</Badge>
                ) : (
                    <Badge variant="warning">Non affecté</Badge>
                ),
        },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: User) => (
                <Badge
                    variant={
                        row.statut === 'Active' ? 'success' :
                            row.statut === 'Pending' ? 'warning' : 'error'
                    }
                >
                    {row.statut}
                </Badge>
            ),
        },
        { key: 'derniereConnexion', title: 'Dernière connexion' },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: User) => (
                <Button
                    variant="secondary"
                    size="small"
                    onClick={() => onViewDetails(row)}
                >
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="users-card">
            <DataTable
                columns={columns}
                data={users}
                pageSize={10}   // active la pagination
            />
        </Card>
    );
}
