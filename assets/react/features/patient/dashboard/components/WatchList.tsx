import { Card } from '@/react/components/UI/Card';
import { WatchItem } from '../types';

interface WatchListProps {
    items: WatchItem[];
}

export function WatchList({ items }: WatchListProps) {
    if (items.length === 0) {
        return (
            <Card className="watch-list">
                <h3>À surveiller</h3>
                <p className="watch-list__empty">Tout semble sous contrôle. Bonne journée !</p>
            </Card>
        );
    }

    return (
        <Card className="watch-list">
            <h3>À surveiller</h3>
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