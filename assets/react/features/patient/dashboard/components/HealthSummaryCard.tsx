import { Card } from '@/react/components/UI/Card';
import { HealthMetric } from '../types';

interface HealthSummaryCardProps {
    metrics: HealthMetric[];
}

function formatDate(iso?: string): string {
    if (!iso) return '';
    const date = new Date(iso);
    if (isNaN(date.getTime())) return '';
    return `le ${date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })}`;
}

export function HealthSummaryCard({ metrics }: HealthSummaryCardProps) {
    return (
        <Card className="health-summary-card">
            <h2>Résumé de santé</h2>
            <div className="health-summary-card__metrics">
                {metrics.map((metric) => (
                    <div
                        key={metric.id}
                        className={`health-metric health-metric--${metric.tone ?? 'neutral'}`}
                    >
                        <span className="health-metric__label">{metric.label}</span>
                        <span className="health-metric__value">
                            {metric.value} <small>{metric.unit}</small>
                        </span>
                        {metric.date && <em className="health-metric__date">{formatDate(metric.date)}</em>}
                    </div>
                ))}
            </div>
        </Card>
    );
}