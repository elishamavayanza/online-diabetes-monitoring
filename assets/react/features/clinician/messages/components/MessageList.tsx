import { useEffect, useRef } from 'react';
import { ConversationThread, Message, MessageAttachment } from '../types';
import { AttachmentPreview } from './AttachmentPreview';
import {
    DeleteIcon,
    DoubleCheckIcon,
    SingleCheckIcon
} from "@/react/features/clinician/messages/components/MessageIcons";

// ==========================================
// ICÔNES SVG LOCALES
// ==========================================

interface MessageListProps {
    thread: ConversationThread;
    onDeleteMessage: (messageId: string) => void;
}

export function MessageList({ thread, onDeleteMessage }: MessageListProps) {
    const messagesContainerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const container = messagesContainerRef.current;
        if (container) {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth',
            });
        }
    }, [thread.messages, thread.id]);

    return (
        <div className="message-thread-wrapper">
            {/* En-tête de la discussion */}
            <div className="message-thread__header">
                <div className="message-thread__participant-info">
                    <div className="message-thread__avatar-placeholder">
                        {thread.participant.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h3>{thread.participant}</h3>
                        <span className="message-thread__status">En ligne</span>
                    </div>
                </div>
            </div>

            {/* Liste des messages */}
            <div className="message-thread__messages" ref={messagesContainerRef}>
                {thread.messages.map((msg: Message, index: number) => {
                    const isMine = msg.emetteur === 'moi';
                    const prevMsg = thread.messages[index - 1];
                    const showDateSeparator = !prevMsg || prevMsg.date !== msg.date;

                    return (
                        <div key={msg.id} className="message-item-wrapper">
                            {showDateSeparator && (
                                <div className="message-date-separator">
                                    <span>{new Date(msg.date).toLocaleDateString()}</span>
                                </div>
                            )}

                            <div
                                className={`message-bubble ${
                                    isMine ? 'message-bubble--mine' : 'message-bubble--other'
                                }`}
                            >
                                {/* Bouton supprimer (visible au survol) */}
                                <button
                                    className="message-bubble__delete-btn"
                                    onClick={() => onDeleteMessage(msg.id)}
                                    title="Supprimer"
                                >
                                    <DeleteIcon />
                                </button>

                                {/* Pièces jointes */}
                                {msg.attachments?.map((attachment: MessageAttachment) => (
                                    <div key={attachment.id} className="message-bubble__attachment">
                                        <AttachmentPreview attachment={attachment} />
                                    </div>
                                ))}

                                {/* Contenu texte */}
                                {msg.contenu && (
                                    <p className="message-bubble__content">{msg.contenu}</p>
                                )}

                                {/* Métadonnées : heure + statut */}
                                <div className="message-bubble__meta">
                                    <span className="message-bubble__time">
                                        {new Date(msg.date).toLocaleTimeString([], {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                        })}
                                    </span>
                                    {isMine && (
                                        <span className="message-bubble__status-icon">
                                            {msg.status === 'read' ? (
                                                <DoubleCheckIcon />
                                            ) : (
                                                <SingleCheckIcon />
                                            )}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
