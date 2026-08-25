import { Card } from '@/react/components/UI/Card';

interface PermissionsListProps {
    permissions: string[];
}

export function PermissionsList({ permissions }: PermissionsListProps) {
    return (
        <Card className="permissions-list">
            <h3>Permissions</h3>
            <ul>
                {permissions.map((perm) => (
                    <li key={perm}>{perm}</li>
                ))}
            </ul>
        </Card>
    );
}
