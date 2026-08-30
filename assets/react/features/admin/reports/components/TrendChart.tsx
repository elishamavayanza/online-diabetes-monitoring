import { TrendSeries } from '../types';

interface TrendChartProps {
    series: TrendSeries;
}

export function TrendChart({ series }: TrendChartProps) {
    const points = series.points;

    if (!points.length) {
        return (
            <div className="trend-chart">
                <h3>{series.label}</h3>
                <p className="trend-chart__empty">Aucune tendance disponible.</p>
            </div>
        );
    }

    const width = 640;
    const height = 220;
    const padding = 28;
    const values = points.map((p) => p.value);
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;

    const coords = points.map((point, index) => {
        const x = padding + (index / Math.max(points.length - 1, 1)) * (width - padding * 2);
        const y = height - padding - ((point.value - min) / range) * (height - padding * 2);
        return { ...point, x, y };
    });

    const polyline = coords.map((p) => `${p.x},${p.y}`).join(' ');

    return (
        <div className="trend-chart">
            <div className="trend-chart__header">
                <h3>{series.label}</h3>
                {series.unit && <span className="trend-chart__unit">{series.unit}</span>}
            </div>
            <svg viewBox={`0 0 ${width} ${height}`} className="trend-chart__svg" role="img">
                <polyline points={polyline} className="trend-chart__line" />
                {coords.map((point) => (
                    <circle key={point.date} cx={point.x} cy={point.y} r="4" className="trend-chart__dot" />
                ))}
            </svg>
            <div className="trend-chart__labels">
                <span>{points[0]?.date}</span>
                <span>{points[points.length - 1]?.date}</span>
            </div>
        </div>
    );
}
