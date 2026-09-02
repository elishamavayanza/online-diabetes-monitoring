// services/messagesService.ts
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';
import { Conversation, ConversationThread, Message, MessageAttachment } from '../types';

interface ConversationSummaryResponse {
    id: string;
    subject: string;
    patientName: string | null;
    lastMessageContent: string | null;
    lastMessageAt: string | null;
    unreadCount: number;
    createdAt: string;
}

interface MessageDetailResponse {
    id: string;
    content: string;
    sentAt: string;
    isMine: boolean;
    attachments: MessageAttachment[];
}

interface MessageResponse {
    id: string;
    conversationId: string;
    senderId: string;
    content: string;
    sentAt: string;
}

// ==========================================
// LECTURE
// ==========================================

export async function fetchConversations(): Promise<Conversation[]> {
    const response = await apiClient.get<ApiFeedback<ConversationSummaryResponse[]>>('/conversations');
    const conversations = unwrapApiData(response.data, 'Erreur lors du chargement des conversations.');

    return conversations.map((conversation) => ({
        id: conversation.id,
        participant: conversation.patientName ?? conversation.subject,
        type: 'Patient',
        dernierMessage: conversation.lastMessageContent ?? '',
        dateDernierMessage: conversation.lastMessageAt ?? conversation.createdAt,
        nonLus: conversation.unreadCount,
    }));
}

export async function fetchConversationThread(id: string, participant = 'Conversation'): Promise<ConversationThread> {
    const response = await apiClient.get<ApiFeedback<MessageDetailResponse[]>>(`/conversations/${id}/messages`);
    const messages = unwrapApiData(response.data, 'Erreur lors du chargement de la discussion.');

    return {
        id,
        participant,
        messages: messages.map((message) => ({
            id: message.id,
            contenu: message.content,
            date: message.sentAt,
            emetteur: message.isMine ? 'moi' : 'autre',
            attachments: message.attachments,
        })),
    };
}

// ==========================================
// ECRITURE (ENVOI DE MESSAGES & FICHIERS)
// ==========================================

export async function postMessage(conversationId: string, content: string): Promise<MessageResponse> {
    const payload = { conversationId, content };

    const response = await apiClient.post<ApiFeedback<MessageResponse>>('/messages', payload);
    return unwrapApiData(response.data, 'Erreur lors de l\'envoi du message.');
}

export async function uploadMessageAttachment(messageId: string, file: File): Promise<unknown> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('messageId', messageId);

    // Content-Type sera défini automatiquement par le navigateur (multipart/form-data)
    const response = await apiClient.post<ApiFeedback<unknown>>('/message-attachments', formData, {
    });
    return unwrapApiData(response.data, 'Erreur lors de l\'envoi du fichier.');
}

export async function markMessageAsRead(messageId: string): Promise<void> {
    const payload = { message: `/api/messages/${messageId}` };
    await apiClient.post('/message-read-receipts', payload);
}
