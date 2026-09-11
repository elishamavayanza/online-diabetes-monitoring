import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Member } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface MembersTableProps {
    members: Member[];
}

export function MembersTable({ members }: MembersTableProps) {
    const { t } = useI18n();

    const columns = [
        { key: 'nom', title: t('Nom') },
        { key: 'role', title: t('Rôle') },
        { key: 'etablissement', title: t('Établissement'), render: (row: Member) => row.etablissement ?? '—' },
        { key: 'departement', title: t('Département'), render: (row: Member) => row.departement ?? '—' },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: Member) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        { key: 'dateArrivee', title: t("Date d'arrivée") },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Member) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    {t('Détails')}
                </Button>
            ),
        },
    ];

    return (
        <Card className="members-card">
            <DataTable columns={columns} data={members} />
        </Card>
    );
}
