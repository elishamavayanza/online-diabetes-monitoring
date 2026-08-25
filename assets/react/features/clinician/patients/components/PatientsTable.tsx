import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { ClinicianPatient } from '../types';

interface PatientsTableProps {
    patients: ClinicianPatient[];
}

export function PatientsTable({ patients }: PatientsTableProps) {
    const columns = [
        { key: 'nom', title: 'Patient' },
        { key: 'derniereConsultation', title: 'Dernière consultation' },
        { key: 'prochainRendezVous', title: 'Prochain rendez-vous' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: ClinicianPatient) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: ClinicianPatient) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Dossier', row.id)}>
                    Dossier
                </Button>
            ),
        },
    ];

    return (
        <Card className="clinician-patients-card">
            <DataTable columns={columns} data={patients} />
        </Card>
    );
}
