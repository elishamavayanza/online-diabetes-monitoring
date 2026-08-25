import { Card } from '@/react/components/UI/Card';
import { HealthMetric } from '../types';

interface HealthSummaryCardProps {
    metrics: HealthMetric[];
}

export function HealthSummaryCard({ metrics }: HealthSummaryCardProps) {
    return (
        <Card className="health-summary-card">
            <h2>Résumé de santé</h2>
            <div className="health-summary-card__metrics">
                {metrics.map((metric) => (
                    <div key={metric.id} className="health-metric">
                        <span className="health-metric__label">{metric.label}</span>
                        <span className="health-metric__value">
                            {metric.value} <small>{metric.unit}</small>
                        </span>
                    </div>
                ))}
            </div>
        </Card>
    );
}
