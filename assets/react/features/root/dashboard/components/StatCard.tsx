import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { StatCardData } from '../types';

interface StatCardProps {
    stat: StatCardData;
}

export function StatCard({ stat }: StatCardProps) {
    return (
        <Card className="stat-card">
            <div className="stat-card__icon">{stat.icon}</div>
            <div className="stat-card__value">{stat.value}</div>
            <div className="stat-card__label">{stat.label}</div>
        </Card>
    );
}
