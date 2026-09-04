// hooks/usePatientMessages.ts
import { useEffect, useState, useCallback } from 'react';
import {
    fetchConversations,
    fetchConversationThread,
    postMessage,
    uploadMessageAttachment,
    deleteMessage as deleteMessageFromApi,
} from '@/react/features/clinician/messages/services/messagesService'; // ✅ chemin absolu
import {
    Conversation,
    ConversationThread,
    MessageAttachment,
} from '@/react/features/clinician/messages/types'; // ✅ types du clinicien


export function usePatientMessages() {
    const [conversations, setConversations] = useState<Conversation[]>([]);
    const [selectedConversation, setSelectedConversation] = useState<ConversationThread | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [sendError, setSendError] = useState<string | null>(null);

    // Chargement initial
    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
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
    const selectConversation = useCallback(async (id: string) => {
        try {
            setSendError(null);
            const conversation = conversations.find((c) => c.id === id);
            const participant = conversation?.participant ?? 'Conversation';
            const thread = await fetchConversationThread(id, participant);
            setSelectedConversation(thread);
        } catch (err) {
            setSendError('Impossible de charger la conversation.');
        }
    }, [conversations]);

    // Envoi d'un message (texte ou fichier)
    const sendMessage = useCallback(async (threadId: string, texte: string, fichier?: File) => {
        setSendError(null);
        if (!texte.trim() && !fichier) return;

        const participant = selectedConversation?.participant ?? 'Conversation';
        const tempId = `temp-${Date.now()}`;
        const newMessage = {
            id: tempId,
            contenu: fichier ? `Fichier: ${fichier.name}` : texte,
            date: new Date().toISOString(),
            emetteur: 'moi' as const,
            attachments: fichier ? [{
                id: `temp-att-${Date.now()}`,
                fileName: fichier.name,
                mimeType: fichier.type || 'application/octet-stream',
                fileUrl: URL.createObjectURL(fichier),
            }] : [],
        };

        // Optimistic update
        setSelectedConversation(prev => prev ? {
            ...prev,
            messages: [...prev.messages, newMessage],
        } : prev);

        try {
            const contentToSend = texte.trim() || (fichier ? `Fichier: ${fichier.name}` : '');
            const savedMessage = await postMessage(threadId, contentToSend);
            if (fichier && savedMessage.id) {
                await uploadMessageAttachment(savedMessage.id, fichier);
            }

            // Recharger le thread pour obtenir les vraies données
            const updatedThread = await fetchConversationThread(threadId, participant);
            setSelectedConversation(updatedThread);

            // Actualiser la liste des conversations
            const updatedConversations = await fetchConversations();
            setConversations(updatedConversations);
        } catch (err) {
            // Rollback du message optimiste
            setSelectedConversation(prev => prev ? {
                ...prev,
                messages: prev.messages.filter(m => m.id !== tempId),
            } : prev);
            setSendError("Erreur lors de l'envoi du message.");
        }
    }, [selectedConversation]);

    // Suppression d'un message (optionnel)
    const deleteMessage = useCallback(async (messageId: string) => {
        if (!selectedConversation) return;
        const previousMessages = selectedConversation.messages;
        setSelectedConversation(prev => prev ? {
            ...prev,
            messages: prev.messages.filter(m => m.id !== messageId),
        } : prev);

        try {
            await deleteMessageFromApi(messageId);
            const refreshed = await fetchConversationThread(selectedConversation.id, selectedConversation.participant);
            setSelectedConversation(refreshed);
            const updatedConversations = await fetchConversations();
            setConversations(updatedConversations);
        } catch (err) {
            setSelectedConversation(prev => prev ? { ...prev, messages: previousMessages } : prev);
            setSendError('Erreur lors de la suppression du message.');
        }
    }, [selectedConversation]);

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
