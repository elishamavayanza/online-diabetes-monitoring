import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { MeasurementRecord } from '../types';

interface MeasurementsTableProps {
    records: MeasurementRecord[];
}

export function MeasurementsTable({ records }: MeasurementsTableProps) {
    const columns = [
        { key: 'date', title: 'Date' },
        { key: 'value', title: 'Valeur' },
        { key: 'note', title: 'Note', render: (row: MeasurementRecord) => row.note ?? '—' },
    ];

    return (
        <Card className="measurements-card">
            <DataTable columns={columns} data={records} />
        </Card>
    );
}
