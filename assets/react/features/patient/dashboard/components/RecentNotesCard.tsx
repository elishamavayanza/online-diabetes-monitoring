import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';
import { RecentNote } from '../types';

interface RecentNotesCardProps {
    notes: RecentNote[];
}

function formatDate(iso: string): string {
    const date = new Date(iso);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
}

export function RecentNotesCard({ notes }: RecentNotesCardProps) {
    const { t } = useI18n();

    if (notes.length === 0) {
        return (
            <Card className="recent-notes-card">
                <h3>{t('Notes médicales récentes')}</h3>
                <p className="recent-notes-card__empty">{t('Aucune note pour le moment.')}</p>
            </Card>
        );
    }

    return (
        <Card className="recent-notes-card">
            <h3>{t('Notes médicales récentes')}</h3>
            <ul>
                {notes.map((note) => (
                    <li key={note.id} className="recent-notes-card__item">
                        <p className="recent-notes-card__content">{note.content}</p>
                        <span className="recent-notes-card__meta">
                            {formatDate(note.date)}
                            {note.authorName ? ` — ${note.authorName}` : ''}
                        </span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}