import { Card } from '@/react/components/UI/Card';
import { ClinicianStat } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface StatCardProps {
    stat: ClinicianStat;
}

export function StatCard({ stat }: StatCardProps) {
    const { t } = useI18n();

    return (
        <Card className="stat-card">
            <div className="stat-card__value">{stat.value}</div>
            <div className="stat-card__label">{t(stat.label)}</div>
        </Card>
    );
}
