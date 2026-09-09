import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Professional } from '../types/types';

interface ProfessionalsTableProps {
    professionals: Professional[];
    onViewDetails?: (professional: Professional) => void;
    onSuspend?: (professional: Professional) => void;
    onReactivate?: (professional: Professional) => void;
}

export function ProfessionalsTable({ professionals, onViewDetails, onSuspend, onReactivate }: ProfessionalsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'type', title: 'Type' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Professional) => (
                <Badge variant={row.statut === 'Active' ? 'success' : row.statut === 'Suspended' ? 'warning' : 'error'}>
                    {row.statut === 'Suspended' ? 'Suspendu' : row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Professional) => (
                <div className="professionals-table__actions">
                    <Button
                        variant="secondary"
                        size="small"
                        onClick={() => onViewDetails?.(row)}
                    >
                        Détails
                    </Button>
                    {row.statut === 'Suspended' ? (
                        <Button
                            variant="success"
                            size="small"
                            onClick={() => onReactivate?.(row)}
                        >
                            Réactiver
                        </Button>
                    ) : (
                        <Button
                            variant="danger"
                            size="small"
                            onClick={() => onSuspend?.(row)}
                        >
                            Suspendre
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    return (
        <Card className="professionals-card">
            <DataTable columns={columns} data={professionals} />
        </Card>
    );
}
