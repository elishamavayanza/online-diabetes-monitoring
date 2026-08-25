import { Card } from '@/react/components/UI/Card';
import { WatchItem } from '../types';

interface WatchListProps {
    items: WatchItem[];
}

export function WatchList({ items }: WatchListProps) {
    return (
        <Card className="watch-list">
            <h3>À surveiller</h3>
            <ul>
                {items.map((item) => (
                    <li key={item.id}>{item.message}</li>
                ))}
            </ul>
        </Card>
    );
}
