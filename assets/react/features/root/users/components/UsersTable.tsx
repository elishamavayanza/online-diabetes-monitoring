import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { User } from '../types';

interface UsersTableProps {
    users: User[];
    total: number;
    page: number;
    limit: number;
    loading: boolean;
    onPageChange: (page: number) => void;
    onSort: (key: string, direction: 'asc' | 'desc') => void;
    onViewDetails: (user: User) => void;
}

export function UsersTable({
                               users,
                               total,
                               page,
                               limit,
                               loading,
                               onPageChange,
                               onSort,
                               onViewDetails,
                           }: UsersTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom', sortable: true },
        { key: 'email', title: 'Email', sortable: true },
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
                <div className="users-table__actions">
                    <Button
                        variant="secondary"
                        size="small"
                        onClick={() => onViewDetails(row)}
                    >
                        Détails
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <Card className="users-card">
            <DataTable
                columns={columns}
                data={users}
                mode="server"
                loading={loading}
                pageSize={limit}
                totalItems={total}
                currentPage={page}
                onPageChange={onPageChange}
                onSort={onSort}
            />
        </Card>
    );
}