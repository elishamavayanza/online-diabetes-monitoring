import { Card } from '@/react/components/UI/Card';
import { ClinicianStat } from '../types';

interface StatCardProps {
    stat: ClinicianStat;
}

export function StatCard({ stat }: StatCardProps) {
    return (
        <Card className="stat-card">
            <div className="stat-card__value">{stat.value}</div>
            <div className="stat-card__label">{stat.label}</div>
        </Card>
    );
}
