import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { DataAccessLog } from '../types';

interface DataAccessLogsTableProps {
    logs: DataAccessLog[];
}

export function DataAccessLogsTable({ logs }: DataAccessLogsTableProps) {
    const columns = [
        { key: 'utilisateur', title: 'Utilisateur' },
        { key: 'patient', title: 'Patient' },
        { key: 'ressourceConsultee', title: 'Ressource consultée' },
        { key: 'motif', title: 'Motif' },
        { key: 'date', title: 'Date' },
    ];

    return (
        <Card className="audit-card">
            <h2>Accès aux données médicales</h2>
            <DataTable columns={columns} data={logs} />
        </Card>
    );
}
