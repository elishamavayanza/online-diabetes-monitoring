import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { PlatformStatusItem } from '../types';

interface PlatformStatusProps {
    items: PlatformStatusItem[];
}

export function PlatformStatus({ items }: PlatformStatusProps) {
    return (
        <Card className="platform-status">
            <h2 className="section-title">État de la plateforme</h2>
            <ul className="platform-status__list">
                {items.map((item) => (
                    <li key={item.id} className="platform-status__item">
                        <span className="platform-status__label">{item.label}</span>
                        <Badge variant={item.isActive ? 'success' : 'error'}>
                            {item.isActive ? '✓' : '✗'}
                        </Badge>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
