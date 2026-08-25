import { Card } from '@/react/components/UI/Card';
import { NutritionistStat } from '../types';

interface StatCardProps {
    stat: NutritionistStat;
}

export function StatCard({ stat }: StatCardProps) {
    return (
        <Card className="stat-card">
            <div className="stat-card__value">{stat.value}</div>
            <div className="stat-card__label">{stat.label}</div>
        </Card>
    );
}
