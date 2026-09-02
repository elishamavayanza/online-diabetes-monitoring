// hooks/useMessages.ts
import { useEffect, useState } from 'react';
import {
    fetchConversations,
    fetchConversationThread,
    postMessage,
    uploadMessageAttachment
} from '../services/messagesService';
import { Conversation, ConversationThread, Message } from '../types';

export function useMessages() {
    const [conversations, setConversations] = useState<Conversation[]>([]);
    const [selectedConversation, setSelectedConversation] = useState<ConversationThread | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [sendError, setSendError] = useState<string | null>(null);

    // Chargement initial
    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchConversations();
                setConversations(data);
                if (data.length > 0) {
                    const thread = await fetchConversationThread(data[0].id, data[0].participant);
                    setSelectedConversation(thread);
                }
            } catch (err) {
                setError('Impossible de charger les conversations.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    // Sélection d'une conversation
    const selectConversation = async (id: string) => {
        try {
            setSendError(null);
            const participant = conversations.find((conversation) => conversation.id === id)?.participant;
            const thread = await fetchConversationThread(id, participant);
            setSelectedConversation(thread);
        } catch (err) {
            setSendError('Impossible de charger la conversation.');
        }
    };

    // Envoi d'un message (Texte et/ou Fichier)
    const sendMessage = async (threadId: string, texte: string, fichier?: File) => {
        setSendError(null);
        const participant = selectedConversation?.participant;
        // 1. Mise à jour optimiste
        const tempId = `temp-${Date.now()}`;
        const newMessage: Message = {
            id: tempId,
            contenu: fichier ? `Fichier: ${fichier.name}` : texte,
            date: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
            emetteur: 'moi'
        };

        setSelectedConversation(prev =>
            prev ? { ...prev, messages: [...prev.messages, newMessage] } : prev
        );

        try {
            // 2. Appel API pour créer le message texte
            const contentToSend = texte.trim() || (fichier ? `Fichier: ${fichier.name}` : '');
            const savedMessage = await postMessage(threadId, contentToSend);

            // 3. Appel API pour uploader le fichier en liant l'ID du message créé
            if (fichier && savedMessage.id) {
                await uploadMessageAttachment(savedMessage.id, fichier);
            }

            // 4. Mettre à jour le message optimiste avec l'ID du serveur (et le contenu si disponible)
            setSelectedConversation(prev => {
                if (!prev) return prev;
                return {
                    ...prev,
                    messages: prev.messages.map(m => {
                        if (m.id === tempId) {
                            return {
                                ...m,
                                id: savedMessage.id,
                                // Utilisation de 'content' si présent, sinon on garde le contenu optimiste
                                contenu: savedMessage.content ?? m.contenu,
                                // On garde la date optimiste (pas de 'createdAt' sur MessageResponse)
                            };
                        }
                        return m;
                    }),
                };
            });

        } catch (err) {
            console.error(err);
            setSendError("Erreur lors de l'envoi du message.");
            // En cas d'erreur, on retire le message temporaire
            setSelectedConversation(prev =>
                prev ? { ...prev, messages: prev.messages.filter(m => m.id !== tempId) } : prev
            );
        }
    };

    return {
        conversations,
        selectedConversation,
        selectConversation,
        sendMessage,
        isLoading,
        error,
        sendError,
        clearSendError: () => setSendError(null)
    };
}
