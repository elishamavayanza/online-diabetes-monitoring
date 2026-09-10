import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';
import { formattingLocale } from '@/react/i18n/formatters';
import { HealthMetric } from '../types';

interface HealthSummaryCardProps {
    metrics: HealthMetric[];
}

function formatDate(
    iso: string | undefined,
    t: (key: string, values?: Record<string, string | number>) => string,
    locale: 'fr' | 'en'
): string {
    if (!iso) return '';
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return '';
    const formatted = date.toLocaleDateString(formattingLocale(locale), {
        day: '2-digit',
        month: 'short',
    });
    return t('le {{ date }}', { date: formatted });
}

export function HealthSummaryCard({ metrics }: HealthSummaryCardProps) {
    const { t, locale } = useI18n();

    return (
        <Card className="health-summary-card">
            <h2>{t('Résumé de santé')}</h2>
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
                        {metric.date && <em className="health-metric__date">{formatDate(metric.date, t, locale)}</em>}
                    </div>
                ))}
            </div>
        </Card>
    );
}