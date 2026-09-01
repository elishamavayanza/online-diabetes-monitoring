// components/CandlestickChart.tsx
import React from 'react';
import {
    CandlestickDataPoint,
    useCandlestickChart
} from "@/react/hook-components/Data/CandlestickChart/useCandlestickChart";

interface CandlestickChartProps {
    data: CandlestickDataPoint[];
    width?: number;
    height?: number;
    margin?: { top: number; right: number; bottom: number; left: number };
    upColor?: string;
    downColor?: string;
    showAxis?: boolean;
    formatDate?: (date: string | number) => string;
    formatPrice?: (price: number) => string;
}

export function CandlestickChart({
                                     data,
                                     width = 600,
                                     height = 300,
                                     margin,
                                     upColor,
                                     downColor,
                                     showAxis = true,
                                     formatDate = (d) => String(d),
                                     formatPrice = (p) => p.toFixed(2),
                                 }: CandlestickChartProps) {
    const {
        width: w,
        height: h,
        margin: m,
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
        upColor: up,
        downColor: down,
    } = useCandlestickChart({ data, width, height, margin, upColor, downColor });

    if (data.length === 0) {
        return <div className="candlestick-chart__empty">Aucune donnée</div>;
    }

    return (
        <div
            className="candlestick-chart"
            style={{ width: w, height: h }}
            onMouseLeave={handleMouseLeave}
        >
            <svg width={w} height={h}>
                {/* Axe horizontal (dates) */}
                {showAxis && (
                    <g className="candlestick-chart__axis">
                        {data.map((d, i) => (
                            i % Math.ceil(data.length / 6) === 0 && (
                                <text
                                    key={i}
                                    x={getX(i)}
                                    y={h - 5}
                                    textAnchor="middle"
                                    fontSize="10"
                                    fill="currentColor"
                                >
                                    {formatDate(d.date)}
                                </text>
                            )
                        ))}
                    </g>
                )}

                {/* Axe vertical (prix) */}
                {showAxis && (
                    <g className="candlestick-chart__axis">
                        {[maxPrice, (maxPrice + minPrice) / 2, minPrice].map((price, idx) => (
                            <text
                                key={idx}
                                x={m.left - 5}
                                y={getY(price) + 4}
                                textAnchor="end"
                                fontSize="10"
                                fill="currentColor"
                            >
                                {formatPrice(price)}
                            </text>
                        ))}
                    </g>
                )}

                {/* Chandeliers */}
                {data.map((d, i) => {
                    const isUp = d.close >= d.open;
                    const color = isUp ? up : down;
                    const x = getX(i) - candleWidth / 2;
                    const yOpen = getY(d.open);
                    const yClose = getY(d.close);
                    const yHigh = getY(d.high);
                    const yLow = getY(d.low);

                    const bodyTop = Math.min(yOpen, yClose);
                    const bodyHeight = Math.max(Math.abs(yOpen - yClose), 1);

                    return (
                        <g
                            key={i}
                            onMouseEnter={() => handleMouseMove(i)}
                            className="candlestick-chart__candle"
                            style={{ cursor: 'pointer' }}
                        >
                            {/* Mèche haute/basse */}
                            <line
                                x1={getX(i)}
                                x2={getX(i)}
                                y1={yHigh}
                                y2={yLow}
                                stroke={color}
                                strokeWidth={1}
                            />
                            {/* Corps */}
                            <rect
                                x={x}
                                y={bodyTop}
                                width={candleWidth}
                                height={bodyHeight}
                                fill={isUp ? color : color}
                                rx={1}
                                stroke="none"
                            />
                        </g>
                    );
                })}
            </svg>

            {/* Tooltip */}
            {hoveredIndex !== null && (
                <div
                    className="candlestick-chart__tooltip"
                    style={{
                        left: getX(hoveredIndex),
                        top: Math.min(getY(data[hoveredIndex].high), getY(data[hoveredIndex].low)) - 10,
                    }}
                >
                    <div className="candlestick-chart__tooltip-date">
                        {formatDate(data[hoveredIndex].date)}
                    </div>
                    <div>Open : {formatPrice(data[hoveredIndex].open)}</div>
                    <div>High : {formatPrice(data[hoveredIndex].high)}</div>
                    <div>Low : {formatPrice(data[hoveredIndex].low)}</div>
                    <div>Close : {formatPrice(data[hoveredIndex].close)}</div>
                </div>
            )}
        </div>
    );
}
