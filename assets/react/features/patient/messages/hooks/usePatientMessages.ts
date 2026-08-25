import { useEffect, useState } from 'react';
import { fetchConversations, fetchConversationThread } from '../services/patientMessagesService';
import { Conversation, ConversationThread } from '../types';

export function usePatientMessages() {
    const [conversations, setConversations] = useState<Conversation[]>([]);
    const [selectedConversation, setSelectedConversation] = useState<ConversationThread | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchConversations();
                setConversations(data);
                if (data.length > 0) {
                    const thread = await fetchConversationThread(data[0].id);
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

    const selectConversation = async (id: string) => {
        const thread = await fetchConversationThread(id);
        setSelectedConversation(thread);
    };

    return { conversations, selectedConversation, selectConversation, isLoading, error };
}
