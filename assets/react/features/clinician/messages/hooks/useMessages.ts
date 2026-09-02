// hooks/useMessages.ts
import { useEffect, useState } from 'react';

import {
    fetchConversations,
    fetchConversationThread,
    postMessage,
    uploadMessageAttachment,
    deleteMessage as deleteMessageFromApi,
} from '../services/messagesService';

import {
    Conversation,
    ConversationThread,
    Message,
    MessageAttachment,
} from '../types';

export function useMessages(initialConversationId?: string) {
    const [conversations, setConversations] = useState<Conversation[]>([]);
    const [selectedConversation, setSelectedConversation] =
        useState<ConversationThread | null>(null);

    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [sendError, setSendError] = useState<string | null>(null);

    // ============================================================
    // CHARGEMENT INITIAL (fusionné)
    // ============================================================
    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchConversations();
                setConversations(data);

                if (data.length > 0) {
                    // Déterminer l'ID de conversation à ouvrir
                    const targetId =
                        initialConversationId &&
                        data.some((c) => c.id === initialConversationId)
                            ? initialConversationId
                            : data[0].id;

                    const participant =
                        data.find((c) => c.id === targetId)?.participant;

                    const thread = await fetchConversationThread(
                        targetId,
                        participant
                    );
                    setSelectedConversation(thread);
                }
            } catch (err) {
                console.error(err);
                setError('Impossible de charger les conversations.');
            } finally {
                setIsLoading(false);
            }
        };

        void load();
    }, [initialConversationId]);

    // ============================================================
    // RECHARGER LA LISTE DES CONVERSATIONS
    // ============================================================
    const refreshConversations = async () => {
        try {
            const data = await fetchConversations();
            setConversations(data);
            return data;
        } catch (err) {
            console.error('Erreur lors du rafraîchissement des conversations:', err);
            return null;
        }
    };

    // ============================================================
    // RECHARGER LE THREAD COURANT
    // ============================================================
    const refreshCurrentThread = async (
        conversationId: string,
        participant?: string
    ) => {
        try {
            const thread = await fetchConversationThread(
                conversationId,
                participant
            );
            setSelectedConversation(thread);
            return thread;
        } catch (err) {
            console.error('Erreur lors du rafraîchissement du thread:', err);
            return null;
        }
    };

    // ============================================================
    // SÉLECTION D'UNE CONVERSATION
    // ============================================================
    const selectConversation = async (id: string) => {
        try {
            setSendError(null);

            const conversation = conversations.find((item) => item.id === id);
            const participant = conversation?.participant ?? 'Conversation';

            const thread = await fetchConversationThread(id, participant);
            setSelectedConversation(thread);
        } catch (err) {
            console.error('Erreur sélection conversation:', err);
            setSendError('Impossible de charger la conversation.');
        }
    };

    // ============================================================
    // SUPPRESSION D'UN MESSAGE
    // ============================================================
    const deleteMessage = async (messageId: string) => {
        const currentThread = selectedConversation;
        if (!currentThread) return;

        const conversationId = currentThread.id;
        const participant = currentThread.participant;
        const previousMessages = currentThread.messages;

        // Suppression optimiste
        setSelectedConversation((prev) =>
            prev
                ? {
                    ...prev,
                    messages: prev.messages.filter(
                        (message) => message.id !== messageId
                    ),
                }
                : prev
        );

        try {
            await deleteMessageFromApi(messageId);

            // Recharger le thread depuis le serveur
            const refreshedThread = await fetchConversationThread(
                conversationId,
                participant
            );
            setSelectedConversation(refreshedThread);

            // Actualiser la liste des conversations
            const updatedConversations = await fetchConversations();
            setConversations(updatedConversations);
        } catch (err) {
            console.error('Erreur suppression message:', err);
            // Rollback
            setSelectedConversation((prev) =>
                prev ? { ...prev, messages: previousMessages } : prev
            );
            setSendError('Erreur lors de la suppression du message.');
        }
    };

    // ============================================================
    // ENVOI D'UN MESSAGE
    // ============================================================
    const sendMessage = async (
        threadId: string,
        texte: string,
        fichier?: File
    ) => {
        setSendError(null);

        if (!texte.trim() && !fichier) return;

        const participant =
            selectedConversation?.participant ??
            conversations.find((c) => c.id === threadId)?.participant ??
            'Conversation';

        const tempId =
            typeof crypto !== 'undefined' &&
            typeof crypto.randomUUID === 'function'
                ? `temp-${crypto.randomUUID()}`
                : `temp-${Date.now()}-${Math.random()}`;

        let localObjectUrl: string | null = null;
        let optimisticAttachment: MessageAttachment | undefined;

        if (fichier) {
            localObjectUrl = URL.createObjectURL(fichier);
            const attachmentId =
                typeof crypto !== 'undefined' &&
                typeof crypto.randomUUID === 'function'
                    ? `temp-attachment-${crypto.randomUUID()}`
                    : `temp-attachment-${Date.now()}-${Math.random()}`;

            let mimeType = fichier.type || 'application/octet-stream';
            if (fichier.name.toLowerCase().startsWith('vocal-')) {
                mimeType = 'audio/webm';
            }

            optimisticAttachment = {
                id: attachmentId,
                fileName: fichier.name,
                mimeType,
                fileUrl: localObjectUrl,
            };
        }

        const newMessage: Message = {
            id: tempId,
            contenu: texte.trim(),
            date: new Date().toISOString(),
            emetteur: 'moi',
            attachments: optimisticAttachment ? [optimisticAttachment] : [],
        };

        setSelectedConversation((prev) =>
            prev
                ? { ...prev, messages: [...prev.messages, newMessage] }
                : prev
        );

        try {
            const contentToSend =
                texte.trim() || (fichier ? `Fichier: ${fichier.name}` : '');

            if (!contentToSend) {
                throw new Error('Le contenu du message est vide.');
            }

            const savedMessage = await postMessage(threadId, contentToSend);

            if (fichier && savedMessage.id) {
                await uploadMessageAttachment(savedMessage.id, fichier);
            }

            // Recharger le thread depuis le serveur pour obtenir les vraies données
            const updatedThread = await fetchConversationThread(
                threadId,
                participant
            );
            setSelectedConversation(updatedThread);

            // Actualiser la liste des conversations
            await refreshConversations();

            if (localObjectUrl) {
                URL.revokeObjectURL(localObjectUrl);
                localObjectUrl = null;
            }
        } catch (err) {
            console.error('Erreur lors de l’envoi:', err);
            setSendError("Erreur lors de l'envoi du message.");

            // Supprimer le message optimiste
            setSelectedConversation((prev) =>
                prev
                    ? {
                        ...prev,
                        messages: prev.messages.filter(
                            (message) => message.id !== tempId
                        ),
                    }
                    : prev
            );

            if (localObjectUrl) {
                URL.revokeObjectURL(localObjectUrl);
                localObjectUrl = null;
            }
        }
    };

    // ============================================================
    // RETOUR
    // ============================================================
    return {
        conversations,
        selectedConversation,
        selectConversation,
        sendMessage,
        deleteMessage,
        isLoading,
        error,
        sendError,
        clearSendError: () => setSendError(null),
    };
}
