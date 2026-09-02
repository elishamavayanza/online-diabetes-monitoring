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

export function useMessages() {
    const [conversations, setConversations] = useState<Conversation[]>([]);
    const [selectedConversation, setSelectedConversation] =
        useState<ConversationThread | null>(null);

    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [sendError, setSendError] = useState<string | null>(null);

    // ============================================================
    // RECHARGER LA LISTE DES CONVERSATIONS
    // ============================================================

    const refreshConversations = async () => {
        try {
            const data = await fetchConversations();

            setConversations(data);

            return data;
        } catch (err) {
            console.error(
                'Erreur lors du rafraîchissement des conversations:',
                err
            );

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
            console.error(
                'Erreur lors du rafraîchissement du thread:',
                err
            );

            return null;
        }
    };

    // ============================================================
    // CHARGEMENT INITIAL
    // ============================================================

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchConversations();

                setConversations(data);

                if (data.length > 0) {
                    const thread =
                        await fetchConversationThread(
                            data[0].id,
                            data[0].participant
                        );

                    setSelectedConversation(thread);
                }
            } catch (err) {
                console.error(err);

                setError(
                    'Impossible de charger les conversations.'
                );
            } finally {
                setIsLoading(false);
            }
        };

        void load();
    }, []);

    // ============================================================
    // SÉLECTION D'UNE CONVERSATION
    // ============================================================

    const selectConversation = async (id: string) => {
        try {
            setSendError(null);

            const conversation =
                conversations.find(
                    (item) => item.id === id
                );

            const participant =
                conversation?.participant ?? 'Conversation';

            const thread =
                await fetchConversationThread(
                    id,
                    participant
                );

            setSelectedConversation(thread);
        } catch (err) {
            console.error(
                'Erreur sélection conversation:',
                err
            );

            setSendError(
                'Impossible de charger la conversation.'
            );
        }
    };

    // ============================================================
    // SUPPRESSION D'UN MESSAGE
    // ============================================================

    const deleteMessage = async (
        messageId: string
    ) => {
        if (!selectedConversation) {
            return;
        }

        const conversationId =
            selectedConversation.id;

        const participant =
            selectedConversation.participant;

        // Sauvegarde pour rollback
        const previousMessages =
            selectedConversation.messages;

        // --------------------------------------------------------
        // SUPPRESSION OPTIMISTE
        // --------------------------------------------------------

        setSelectedConversation((prev) => {
            if (!prev) {
                return prev;
            }

            return {
                ...prev,

                messages:
                    prev.messages.filter(
                        (message) =>
                            message.id !== messageId
                    ),
            };
        });

        try {
            // ----------------------------------------------------
            // SUPPRESSION API
            // ----------------------------------------------------

            await deleteMessageFromApi(
                messageId
            );

            // ----------------------------------------------------
            // RECHARGER LE THREAD
            // ----------------------------------------------------

            const refreshedThread =
                await fetchConversationThread(
                    conversationId,
                    participant
                );

            setSelectedConversation(
                refreshedThread
            );

            // ----------------------------------------------------
            // RECHARGER LA LISTE DES CONVERSATIONS
            // ----------------------------------------------------

            await refreshConversations();

        } catch (err) {
            console.error(
                'Erreur suppression message:',
                err
            );

            // ----------------------------------------------------
            // ROLLBACK
            // ----------------------------------------------------

            setSelectedConversation(
                (prev) => {
                    if (!prev) {
                        return prev;
                    }

                    return {
                        ...prev,
                        messages:
                        previousMessages,
                    };
                }
            );

            setSendError(
                'Erreur lors de la suppression du message.'
            );
        }
    };

    // ============================================================
    // ENVOI D'UN MESSAGE
    // TEXTE / IMAGE / DOCUMENT / AUDIO / VIDÉO
    // ============================================================

    const sendMessage = async (
        threadId: string,
        texte: string,
        fichier?: File
    ) => {
        setSendError(null);

        // --------------------------------------------------------
        // VÉRIFICATION
        // --------------------------------------------------------

        if (!texte.trim() && !fichier) {
            return;
        }

        // --------------------------------------------------------
        // CONVERSATION COURANTE
        // --------------------------------------------------------

        const participant =
            selectedConversation?.participant ??
            conversations.find(
                (conversation) =>
                    conversation.id === threadId
            )?.participant ??
            'Conversation';

        // --------------------------------------------------------
        // ID TEMPORAIRE UNIQUE
        // --------------------------------------------------------

        const tempId =
            typeof crypto !== 'undefined' &&
            typeof crypto.randomUUID === 'function'
                ? `temp-${crypto.randomUUID()}`
                : `temp-${Date.now()}-${Math.random()}`;

        // --------------------------------------------------------
        // URL LOCALE
        // --------------------------------------------------------

        let localObjectUrl: string | null =
            null;

        let optimisticAttachment:
            | MessageAttachment
            | undefined;

        // --------------------------------------------------------
        // ATTACHMENT OPTIMISTE
        // --------------------------------------------------------

        if (fichier) {
            localObjectUrl =
                URL.createObjectURL(fichier);

            const attachmentId =
                typeof crypto !== 'undefined' &&
                typeof crypto.randomUUID === 'function'
                    ? `temp-attachment-${crypto.randomUUID()}`
                    : `temp-attachment-${Date.now()}-${Math.random()}`;

            let mimeType =
                fichier.type ||
                'application/octet-stream';

            // Certains MediaRecorder retournent
            // video/webm au lieu de audio/webm.
            if (
                fichier.name
                    .toLowerCase()
                    .startsWith('vocal-')
            ) {
                mimeType = 'audio/webm';
            }

            optimisticAttachment = {
                id: attachmentId,

                fileName: fichier.name,

                mimeType,

                fileUrl: localObjectUrl,
            };
        }

        // --------------------------------------------------------
        // MESSAGE OPTIMISTE
        // --------------------------------------------------------

        const newMessage: Message = {
            id: tempId,

            /*
             * IMPORTANT :
             * Pour un fichier/audio seul, on ne met pas
             * "content": vide visuellement.
             */
            contenu: texte.trim(),

            date: new Date().toISOString(),

            emetteur: 'moi',

            attachments:
                optimisticAttachment
                    ? [optimisticAttachment]
                    : [],
        };

        // --------------------------------------------------------
        // AFFICHAGE IMMÉDIAT
        // --------------------------------------------------------

        setSelectedConversation((prev) => {
            if (!prev) {
                return prev;
            }

            return {
                ...prev,

                messages: [
                    ...prev.messages,
                    newMessage,
                ],
            };
        });

        try {
            // ====================================================
            // 1. CRÉATION DU MESSAGE
            // ====================================================

            /*
             * IMPORTANT :
             *
             * L'API refuse probablement content="".
             *
             * Pour un audio/fichier sans texte,
             * on envoie donc une valeur descriptive.
             */
            const contentToSend =
                texte.trim() ||
                (
                    fichier
                        ? `Fichier: ${fichier.name}`
                        : ''
                );

            if (!contentToSend) {
                throw new Error(
                    'Le contenu du message est vide.'
                );
            }

            const savedMessage =
                await postMessage(
                    threadId,
                    contentToSend
                );

            // ====================================================
            // 2. UPLOAD DU FICHIER
            // ====================================================

            if (
                fichier &&
                savedMessage.id
            ) {
                await uploadMessageAttachment(
                    savedMessage.id,
                    fichier
                );
            }

            // ====================================================
            // 3. TRÈS IMPORTANT :
            // RECHARGER LE THREAD APRÈS L'UPLOAD
            // ====================================================

            /*
             * Cela récupère les vraies données Symfony :
             *
             * - ID attachment
             * - URL réelle
             * - MIME réel
             * - nom réel
             * - audio
             * - image
             * - vidéo
             */

            const updatedThread =
                await fetchConversationThread(
                    threadId,
                    participant
                );

            setSelectedConversation(
                updatedThread
            );

            // ====================================================
            // 4. ACTUALISER LA LISTE GAUCHE
            // ====================================================

            await refreshConversations();

            // ====================================================
            // 5. LIBÉRER L'URL LOCALE
            // ====================================================

            if (localObjectUrl) {
                URL.revokeObjectURL(
                    localObjectUrl
                );

                localObjectUrl = null;
            }

        } catch (err) {
            console.error(
                'Erreur lors de l’envoi:',
                err
            );

            setSendError(
                "Erreur lors de l'envoi du message."
            );

            // ====================================================
            // SUPPRESSION DU MESSAGE OPTIMISTE
            // ====================================================

            setSelectedConversation(
                (prev) => {
                    if (!prev) {
                        return prev;
                    }

                    return {
                        ...prev,

                        messages:
                            prev.messages.filter(
                                (message) =>
                                    message.id !==
                                    tempId
                            ),
                    };
                }
            );

            // ====================================================
            // LIBÉRER URL LOCALE
            // ====================================================

            if (localObjectUrl) {
                URL.revokeObjectURL(
                    localObjectUrl
                );

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

        clearSendError: () =>
            setSendError(null),
    };
}
