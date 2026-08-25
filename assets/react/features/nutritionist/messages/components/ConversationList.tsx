import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Conversation } from '../types';

interface ConversationListProps {
    conversations: Conversation[];
    selectedId?: string;
    onSelect: (id: string) => void;
}

export function ConversationList({ conversations, selectedId, onSelect }: ConversationListProps) {
    return (
        <Card className="conversation-list">
            <h2>Conversations</h2>
            <ul>
                {conversations.map((conv) => (
                    <li
                        key={conv.id}
                        className={`conversation-item ${conv.id === selectedId ? 'conversation-item--active' : ''}`}
                        onClick={() => onSelect(conv.id)}
                    >
                        <div className="conversation-item__header">
                            <span>{conv.participant}</span>
                            <span className="conversation-item__type">{conv.type}</span>
                            {conv.nonLus > 0 && <Badge variant="error">{conv.nonLus}</Badge>}
                        </div>
                        <div className="conversation-item__preview">
                            <span>{conv.dernierMessage}</span>
                            <span className="conversation-item__date">{conv.dateDernierMessage}</span>
                        </div>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
