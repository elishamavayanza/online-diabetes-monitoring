import { useEffect, useRef, useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { tokenStorage } from '@/services/storage/storage.service';
import { ConversationThread, Message, MessageAttachment } from '../types';

// Icône pour les fichiers
const FileIcon = () => (
    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
        <polyline points="13 2 13 9 20 9"></polyline>
    </svg>
);

function AttachmentPreview({ attachment }: { attachment: MessageAttachment }) {
    const [objectUrl, setObjectUrl] = useState<string | null>(null);
    const [loadError, setLoadError] = useState(false);

    useEffect(() => {
        const controller = new AbortController();
        const token = tokenStorage.getAccessToken();

        fetch(attachment.fileUrl, {
            headers: token ? { Authorization: `Bearer ${token}` } : {},
            signal: controller.signal,
        })
            .then((response) => {
                if (!response.ok) throw new Error('Téléchargement impossible');
                return response.blob();
            })
            .then((blob) => setObjectUrl(URL.createObjectURL(blob)))
            .catch((error: unknown) => {
                if (!(error instanceof DOMException && error.name === 'AbortError')) setLoadError(true);
            });

        return () => {
            controller.abort();
            setObjectUrl((currentUrl) => {
                if (currentUrl) URL.revokeObjectURL(currentUrl);
                return null;
            });
        };
    }, [attachment.fileUrl]);

    if (loadError) return <span>Pièce jointe indisponible : {attachment.fileName}</span>;
    if (!objectUrl) return <span>Chargement de {attachment.fileName}…</span>;
    if (attachment.mimeType.startsWith('image/')) {
        return <img src={objectUrl} alt={attachment.fileName} className="message-bubble__image" />;
    }
    if (attachment.mimeType.startsWith('audio/')) {
        return <audio controls src={objectUrl} className="message-bubble__audio">Votre navigateur ne lit pas cet audio.</audio>;
    }

    return (
        <a href={objectUrl} download={attachment.fileName} className="message-bubble__file">
            <FileIcon />
            <span>{attachment.fileName}</span>
        </a>
    );
}

interface MessageListProps {
    thread: ConversationThread;
}

export function MessageList({ thread }: MessageListProps) {
    const messagesContainerRef = useRef<HTMLDivElement>(null);

    // Défilement automatique vers le bas à chaque changement de messages ou de conversation
    useEffect(() => {
        const container = messagesContainerRef.current;
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }, [thread.messages, thread.id]);

    return (
        <>
            {/* Entête de la discussion */}
            <div className="message-thread__header">
                <h3>{thread.participant}</h3>
            </div>

            {/* Liste des messages */}
            <div className="message-thread__messages" ref={messagesContainerRef}>
                {thread.messages.map((msg: Message) => {
                    const isMine = msg.emetteur === 'moi';
                    return (
                        <div
                            key={msg.id}
                            className={`message-bubble ${isMine ? 'message-bubble--mine' : 'message-bubble--other'}`}
                        >
                            {msg.contenu && (
                                <p className="message-bubble__content">{msg.contenu}</p>
                            )}
                            {msg.attachments?.map((attachment: MessageAttachment) => (
                                <AttachmentPreview key={attachment.id} attachment={attachment} />
                            ))}
                            <span className="message-bubble__date">{msg.date}</span>
                        </div>
                    );
                })}
            </div>
        </>
    );
}
