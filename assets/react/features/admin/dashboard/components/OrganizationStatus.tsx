import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { OrganizationStatusItem } from '../types';

interface OrganizationStatusProps {
    items: OrganizationStatusItem[];
}

export function OrganizationStatus({ items }: OrganizationStatusProps) {
    return (
        <Card className="org-status">
            <h2 className="section-title">État de l'organisation</h2>
            <ul className="org-status__list">
                {items.map((item) => (
                    <li key={item.id} className="org-status__item">
                        <span className="org-status__label">{item.label}</span>
                        <Badge variant={item.isActive ? 'success' : 'error'}>
                            {item.isActive ? '✓' : '✗'}
                        </Badge>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
