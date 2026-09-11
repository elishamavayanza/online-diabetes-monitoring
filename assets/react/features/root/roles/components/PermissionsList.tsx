import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';

interface PermissionsListProps {
    permissions: string[];
}

export function PermissionsList({ permissions }: PermissionsListProps) {
    const { t } = useI18n();
    return (
        <Card className="permissions-list">
            <h3>{t('Permissions')}</h3>
            <ul>
                {permissions.map((perm) => (
                    <li key={perm}>{perm}</li>
                ))}
            </ul>
        </Card>
    );
}
