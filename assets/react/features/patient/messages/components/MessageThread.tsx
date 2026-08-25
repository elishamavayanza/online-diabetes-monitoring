import { Card } from '@/react/components/UI/Card';
import { ConversationThread } from '../types';

interface MessageThreadProps {
    thread: ConversationThread;
}

export function MessageThread({ thread }: MessageThreadProps) {
    return (
        <Card className="message-thread">
            <div className="message-thread__header">
                <h3>{thread.participant}</h3>
            </div>
            <div className="message-thread__messages">
                {thread.messages.map((msg) => (
                    <div
                        key={msg.id}
                        className={`message-bubble ${msg.emetteur === 'moi' ? 'message-bubble--mine' : 'message-bubble--other'}`}
                    >
                        <p>{msg.contenu}</p>
                        <span className="message-bubble__date">{msg.date}</span>
                    </div>
                ))}
            </div>
        </Card>
    );
}
