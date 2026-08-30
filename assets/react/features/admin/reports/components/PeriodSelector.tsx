import { PeriodPreset } from '../types';

interface PeriodSelectorProps {
    activePeriod?: PeriodPreset;
    customFrom?: string;
    customTo?: string;
    onPeriodChange: (period: PeriodPreset) => void;
    onCustomRangeChange: (from: string, to: string) => void;
}

const PRESETS: { id: PeriodPreset; label: string }[] = [
    { id: 'month', label: 'Mois' },
    { id: 'quarter', label: 'Trimestre' },
    { id: 'year', label: 'Année' },
];

export function PeriodSelector({
    activePeriod,
    customFrom,
    customTo,
    onPeriodChange,
    onCustomRangeChange,
}: PeriodSelectorProps) {
    return (
        <div className="period-selector">
            <div className="period-selector__presets">
                {PRESETS.map((preset) => (
                    <button
                        key={preset.id}
                        type="button"
                        className={`period-selector__btn ${activePeriod === preset.id ? 'is-active' : ''}`}
                        onClick={() => onPeriodChange(preset.id)}
                    >
                        {preset.label}
                    </button>
                ))}
            </div>
            <div className="period-selector__custom">
                <label>
                    Du
                    <input
                        type="date"
                        value={customFrom ?? ''}
                        onChange={(e) => onCustomRangeChange(e.target.value, customTo ?? e.target.value)}
                    />
                </label>
                <label>
                    Au
                    <input
                        type="date"
                        value={customTo ?? ''}
                        onChange={(e) => onCustomRangeChange(customFrom ?? e.target.value, e.target.value)}
                    />
                </label>
            </div>
        </div>
    );
}
