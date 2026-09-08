import { LineChart } from '@/react/components/Data/LineChart/LineChart';
import type { LineChartDataPoint } from '@/react/hook-components/Data/LineChart/useLineChart';
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

    const data: LineChartDataPoint[] = points.map((p) => ({
        date: p.date,
        value: p.value,
    }));

    return (
        <div className="trend-chart">
            <div className="trend-chart__header">
                <h3>{series.label}</h3>
                {series.unit && <span className="trend-chart__unit">{series.unit}</span>}
            </div>
            <LineChart
                data={data}
                height={220}
                formatDate={(d) => String(d)}
                formatValue={(p) => `${p}`}
            />
        </div>
    );
}