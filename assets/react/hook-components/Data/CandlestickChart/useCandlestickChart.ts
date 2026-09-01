// hooks/useCandlestickChart.ts
import { useMemo, useState } from 'react';

export interface CandlestickDataPoint {
    date: string | number; // timestamp ou date affichable
    open: number;
    high: number;
    low: number;
    close: number;
}

interface UseCandlestickChartProps {
    data: CandlestickDataPoint[];
    width?: number;
    height?: number;
    margin?: { top: number; right: number; bottom: number; left: number };
    upColor?: string;
    downColor?: string;
}

export function useCandlestickChart({
                                        data,
                                        width = 600,
                                        height = 300,
                                        margin = { top: 20, right: 20, bottom: 30, left: 50 },
                                        upColor = 'var(--color-success, #2ecc71)',
                                        downColor = 'var(--color-error, #e74c3c)',
                                    }: UseCandlestickChartProps) {
    const [hoveredIndex, setHoveredIndex] = useState<number | null>(null);

    const chartWidth = width - margin.left - margin.right;
    const chartHeight = height - margin.top - margin.bottom;

    // Calculer min/max des prix (high/low)
    const minPrice = useMemo(() => Math.min(...data.map(d => d.low)), [data]);
    const maxPrice = useMemo(() => Math.max(...data.map(d => d.high)), [data]);

    // Échelles
    const xStep = data.length > 0 ? chartWidth / data.length : 0;
    const candleWidth = Math.max(4, xStep * 0.7); // largeur du corps

    const getX = (index: number) => margin.left + xStep * index + xStep / 2;
    const getY = (price: number) => margin.top + ((maxPrice - price) / (maxPrice - minPrice)) * chartHeight;

    // Gérer le survol
    const handleMouseMove = (index: number) => setHoveredIndex(index);
    const handleMouseLeave = () => setHoveredIndex(null);

    return {
        data,
        width,
        height,
        margin,
        chartWidth,
        chartHeight,
        minPrice,
        maxPrice,
        xStep,
        candleWidth,
        getX,
        getY,
        hoveredIndex,
        handleMouseMove,
        handleMouseLeave,
        upColor,
        downColor,
    };
}
