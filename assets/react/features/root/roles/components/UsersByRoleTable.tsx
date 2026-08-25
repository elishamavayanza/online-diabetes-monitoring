import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { UserSummary } from '../types';

interface UsersByRoleTableProps {
    users: UserSummary[];
}

export function UsersByRoleTable({ users }: UsersByRoleTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'email', title: 'Email' },
    ];

    return (
        <Card className="users-by-role">
            <h3>Utilisateurs concernés</h3>
            <DataTable columns={columns} data={users} />
        </Card>
    );
}
