import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { AuditLog } from '../types';

interface AuditLogsTableProps {
    logs: AuditLog[];
}

export function AuditLogsTable({ logs }: AuditLogsTableProps) {
    const columns = [
        { key: 'utilisateur', title: 'Utilisateur' },
        { key: 'action', title: 'Action' },
        { key: 'ressource', title: 'Ressource' },
        { key: 'date', title: 'Date' },
        { key: 'adresseIp', title: 'Adresse IP' },
        {
            key: 'resultat',
            title: 'Résultat',
            render: (row: AuditLog) => (
                <Badge variant={row.resultat === 'SUCCESS' ? 'success' : 'error'}>
                    {row.resultat}
                </Badge>
            ),
        },
    ];

    return (
        <Card className="audit-card">
            <h2>Journaux d’audit</h2>
            <DataTable columns={columns} data={logs} />
        </Card>
    );
}
