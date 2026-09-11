import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';
import { WatchItem } from '../types';

interface WatchListProps {
    items: WatchItem[];
}

export function WatchList({ items }: WatchListProps) {
    const { t } = useI18n();

    if (items.length === 0) {
        return (
            <Card className="watch-list">
                <h3>{t('À surveiller')}</h3>
                <p className="watch-list__empty">{t('Tout semble sous contrôle. Bonne journée !')}</p>
            </Card>
        );
    }

    return (
        <Card className="watch-list">
            <h3>{t('À surveiller')}</h3>
            <ul>
                {items.map((item) => (
                    <li key={item.id} className={`watch-list__item watch-list__item--${item.level ?? 'info'}`}>
                        {item.message}
                    </li>
                ))}
            </ul>
        </Card>
    );
}