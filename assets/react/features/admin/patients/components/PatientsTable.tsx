import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Patient } from '../types';

interface PatientsTableProps {
    patients: Patient[];
    onViewDetails?: (patient: Patient) => void;
    onSuspend?: (patient: Patient) => void;
    onReactivate?: (patient: Patient) => void;
}

export function PatientsTable({ patients, onViewDetails, onSuspend, onReactivate }: PatientsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'dateNaissance', title: 'Date de naissance' },
        { key: 'typeDiabete', title: 'Type de diabète' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Patient) => (
                <Badge variant={row.statut === 'Active' ? 'success' : row.statut === 'Suspended' ? 'warning' : 'error'}>
                    {row.statut === 'Suspended' ? 'Suspendu' : row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Patient) => (
                <div className="patients-table__actions">
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
        <Card className="patients-card">
            <DataTable columns={columns} data={patients} />
        </Card>
    );
}
