import { DistributionItem } from '../types';
import { formatLabel } from '../utils/formatters';

interface DistributionChartProps {
    title: string;
    items: DistributionItem[];
}

export function DistributionChart({ title, items }: DistributionChartProps) {
    if (!items.length) {
        return (
            <div className="distribution-chart">
                <h3>{title}</h3>
                <p className="distribution-chart__empty">Aucune donnée pour cette période.</p>
            </div>
        );
    }

    const max = Math.max(...items.map((item) => item.count), 1);

    return (
        <div className="distribution-chart">
            <h3>{title}</h3>
            <ul className="distribution-chart__list">
                {items.map((item) => (
                    <li key={item.label} className="distribution-chart__item">
                        <div className="distribution-chart__meta">
                            <span>{formatLabel(item.label)}</span>
                            <span>{item.count} ({item.percentage}%)</span>
                        </div>
                        <div className="distribution-chart__bar-track">
                            <div
                                className="distribution-chart__bar-fill"
                                style={{ width: `${(item.count / max) * 100}%` }}
                            />
                        </div>
                    </li>
                ))}
            </ul>
        </div>
    );
}
