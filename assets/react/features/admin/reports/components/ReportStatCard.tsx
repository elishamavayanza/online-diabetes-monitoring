import { Card } from '@/react/components/UI/Card';
import { StatisticValue } from '../types';
import { changeClass, formatChange, formatStatValue } from '../utils/formatters';

interface ReportStatCardProps {
    label: string;
    stat: StatisticValue;
    hint?: string;
}

export function ReportStatCard({ label, stat, hint }: ReportStatCardProps) {
    return (
        <Card className="report-stat-card">
            <div className="report-stat-card__label">{label}</div>
            <div className="report-stat-card__value">{formatStatValue(stat)}</div>
            <div className={`report-stat-card__change report-stat-card__change--${changeClass(stat.changePercent)}`}>
                {formatChange(stat.changePercent)} vs période précédente
            </div>
            {hint && <div className="report-stat-card__hint">{hint}</div>}
        </Card>
    );
}
