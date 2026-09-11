import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { UserSummary } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface UsersByRoleTableProps {
    users: UserSummary[];
}

export function UsersByRoleTable({ users }: UsersByRoleTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'nom', title: t('Nom') },
        { key: 'email', title: t('Email') },
    ];

    return (
        <Card className="users-by-role">
            <h3>{t('Utilisateurs concernés')}</h3>
            <DataTable
                columns={columns}
                data={users}
                pageSize={5}    // active la pagination (5 par page)
            />
        </Card>
    );
}
