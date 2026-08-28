import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { IconButton } from '@/react/components/UI/IconButton';
import { Tooltip } from '@/react/components/UI/Tooltip';
import { Appointment } from '../types/types';
import { EditIcon, CancelIcon, RemindIcon } from './icons';

interface AppointmentsTableProps {
    appointments: Appointment[];
    onEdit?: (appointment: Appointment) => void;
    onCancel?: (appointment: Appointment) => void;
    onRemind?: (appointment: Appointment) => void;
    showActions?: boolean; // nouvelle prop
}

const statusVariant: Record<string, 'success' | 'warning' | 'error'> = {
    Confirmed: 'success',
    Pending: 'warning',
    Cancelled: 'error',
};

export function AppointmentsTable({
                                      appointments,
                                      onEdit = () => {},
                                      onCancel = () => {},
                                      onRemind = () => {},
                                      showActions = true,
                                  }: AppointmentsTableProps) {
    const columns = [
        { key: 'patient', title: 'Patient' },
        { key: 'professionnel', title: 'Professionnel' },
        { key: 'etablissement', title: 'Établissement' },
        { key: 'date', title: 'Date' },
        { key: 'heure', title: 'Heure' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Appointment) => (
                <Badge variant={statusVariant[row.statut]}>
                    {row.statut}
                </Badge>
            ),
        },
        ...(showActions ? [{
            key: 'actions',
            title: 'Actions',
            render: (row: Appointment) => {
                if (row.statut === 'Cancelled') return null;
                return (
                    <div className="appointments-table__actions" style={{ display: 'flex', gap: '0.5rem' }}>
                        <Tooltip content="Modifier">
                            <IconButton onClick={() => onEdit(row)} aria-label="Modifier" icon={<EditIcon />} />
                        </Tooltip>
                        <Tooltip content="Annuler">
                            <IconButton onClick={() => onCancel(row)} aria-label="Annuler" icon={<CancelIcon />} />
                        </Tooltip>
                        <Tooltip content="Rappeler">
                            <IconButton onClick={() => onRemind(row)} aria-label="Rappeler" icon={<RemindIcon />} />
                        </Tooltip>
                    </div>
                );
            },
        }] : []),
    ];

    return (
        <Card className="appointments-card">
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
