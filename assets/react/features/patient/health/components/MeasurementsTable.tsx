import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { MeasurementRecord } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

// Fonction utilitaire pour formater une date ISO en format lisible
function formatDisplayDateTime(dateStr: string): string {
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr; // retourner tel quel si la date est invalide
    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

interface MeasurementsTableProps {
    records: MeasurementRecord[];
}

export function MeasurementsTable({ records }: MeasurementsTableProps) {
    const { t } = useI18n();
    const columns = [
        {
            key: 'date',
            title: t('Date'),
            render: (row: MeasurementRecord) => formatDisplayDateTime(row.date),
        },
        { key: 'value', title: t('Valeur') },
        {
            key: 'note',
            title: t('Note'),
            render: (row: MeasurementRecord) => row.note ?? '—',
        },
    ];

    return (
        <Card className="measurements-card">
            <DataTable columns={columns} data={records} />
        </Card>
    );
}
