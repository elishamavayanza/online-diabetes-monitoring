import { useMemo, useState } from 'react';

export interface LineChartDataPoint {
    date: string | number;
    value: number;
}

export interface XYPoint extends LineChartDataPoint {
    x: number;
    y: number;
}

interface UseLineChartProps {
    data: LineChartDataPoint[];
    width?: number;
    height?: number;
    margin?: { top: number; right: number; bottom: number; left: number };
}

/**
 * Construit une courbe lissée (Catmull-Rom → Bézier cubique) depuis les points.
 */
function buildSmoothPath(points: XYPoint[]): string {
    if (points.length < 2) {
        return points.length === 1 ? `M ${points[0].x} ${points[0].y}` : '';
    }

    let d = `M ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const p0 = points[i - 1] ?? points[i];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[i + 2] ?? p2;

        const cp1x = p1.x + (p2.x - p0.x) / 6;
        const cp1y = p1.y + (p2.y - p0.y) / 6;
        const cp2x = p2.x - (p3.x - p1.x) / 6;
        const cp2y = p2.y - (p3.y - p1.y) / 6;

        d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
    }

    return d;
}

export function useLineChart({
                                 data,
                                 width = 600,
                                 height = 300,
                                 margin = { top: 24, right: 24, bottom: 42, left: 56 },
                             }: UseLineChartProps) {
    const [hoveredIndex, setHoveredIndex] = useState<number | null>(null);

    const chartWidth = width - margin.left - margin.right;
    const chartHeight = height - margin.top - margin.bottom;

    const minValue = data.length > 0 ? Math.min(...data.map((d) => d.value)) : 0;
    const maxValue = data.length > 0 ? Math.max(...data.map((d) => d.value)) : 1;
    const pad = (maxValue - minValue) * 0.08;
    const paddedMin = minValue - pad;
    const paddedMax = maxValue + pad;

    const xStep = data.length > 1 ? chartWidth / (data.length - 1) : 0;

    const getX = (index: number) => margin.left + xStep * index;
    const getY = (value: number) =>
        margin.top + ((paddedMax - value) / (paddedMax - paddedMin)) * chartHeight;

    const coords: XYPoint[] = useMemo(
        () => data.map((d, i) => ({ ...d, x: getX(i), y: getY(d.value) })),
        [data, width, height]
    );

    const linePath = useMemo(() => buildSmoothPath(coords), [coords]);
    const areaPath = useMemo(() => {
        if (coords.length === 0) return '';
        const baseline = margin.top + chartHeight;
        return `${linePath} L ${coords[coords.length - 1].x} ${baseline} L ${coords[0].x} ${baseline} Z`;
    }, [linePath, coords, margin.top, chartHeight]);

    const getHoveredIndex = (clientX: number, rect: { left: number; width: number }): number => {
        if (data.length === 0) return 0;
        const svgX = ((clientX - rect.left) / Math.max(rect.width, 1)) * width;
        const index = Math.round((svgX - margin.left) / Math.max(xStep, 1));
        return Math.min(Math.max(index, 0), data.length - 1);
    };

    const handleMouseMove = (clientX: number, rect: { left: number; width: number }) => {
        setHoveredIndex(getHoveredIndex(clientX, rect));
    };

    const handleMouseLeave = () => setHoveredIndex(null);

    return {
        data,
        width,
        height,
        margin,
        chartWidth,
        chartHeight,
        minValue,
        maxValue,
        xStep,
        getY,
        coords,
        linePath,
        areaPath,
        hoveredIndex,
        handleMouseMove,
        handleMouseLeave,
    };
}