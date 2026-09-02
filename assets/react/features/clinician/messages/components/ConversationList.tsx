import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { SearchInput } from '@/react/components/Forms/SearchInput'; // ✅ adaptez le chemin
import { Conversation } from '../types';

interface ConversationListProps {
    conversations: Conversation[];
    selectedId?: string;
    onSelect: (id: string) => void;
}

// Formate la date du dernier message : heure si aujourd'hui, "Hier", sinon date courte
function formatConversationDate(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    if (date >= today) {
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    } else if (date >= yesterday) {
        return 'Hier';
    } else {
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    }
}

export function ConversationList({ conversations, selectedId, onSelect }: ConversationListProps) {
    const [search, setSearch] = useState('');

    // Filtrage des conversations selon le terme de recherche
    const filtered = conversations.filter((conv) =>
        conv.participant.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <Card className="conversation-list">
            <div className="conversation-list__header">
                {/* Champ de recherche remplace le titre */}
                <SearchInput
                    value={search}
                    placeholder="Rechercher une conversation..."
                    onSearch={(value) => setSearch(value)}
                    fullWidth
                />
            </div>
            <ul className="conversation-list__items">
                {filtered.length === 0 ? (
                    <li className="conversation-list__empty">Aucune conversation trouvée</li>
                ) : (
                    filtered.map((conv) => (
                        <li
                            key={conv.id}
                            className={`conversation-item ${conv.id === selectedId ? 'conversation-item--active' : ''}`}
                            onClick={() => onSelect(conv.id)}
                        >
                            <div className="conversation-item__avatar">
                                {conv.participant.charAt(0).toUpperCase()}
                            </div>
                            <div className="conversation-item__body">
                                <div className="conversation-item__header">
                                    <span className="conversation-item__participant">{conv.participant}</span>
                                    <span className="conversation-item__date">
                                        {formatConversationDate(conv.dateDernierMessage)}
                                    </span>
                                </div>
                                <div className="conversation-item__preview">
                                    <span className="conversation-item__last-message">{conv.dernierMessage}</span>
                                    {conv.nonLus > 0 && (
                                        <Badge variant="error" className="conversation-item__badge">
                                            {conv.nonLus}
                                        </Badge>
                                    )}
                                </div>
                            </div>
                        </li>
                    ))
                )}
            </ul>
        </Card>
    );
}
