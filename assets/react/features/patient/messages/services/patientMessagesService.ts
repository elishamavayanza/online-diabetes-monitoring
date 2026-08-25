import { Conversation, ConversationThread } from '../types';

export async function fetchConversations(): Promise<Conversation[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'c1',
            participant: 'Dr. Jean Dupont',
            type: 'Médecin',
            dernierMessage: 'Vos résultats sont bons.',
            dateDernierMessage: '2026-08-25 08:12',
            nonLus: 2,
        },
        {
            id: 'c2',
            participant: 'Nutritionniste Sarah K.',
            type: 'Nutritionniste',
            dernierMessage: 'Pensez à suivre le plan.',
            dateDernierMessage: '2026-08-24 16:45',
            nonLus: 0,
        },
        {
            id: 'c3',
            participant: 'Équipe de soins',
            type: 'Équipe',
            dernierMessage: 'Votre prochain rendez-vous est confirmé.',
            dateDernierMessage: '2026-08-23 09:30',
            nonLus: 1,
        },
    ];
}

export async function fetchConversationThread(id: string): Promise<ConversationThread> {
    await new Promise((resolve) => setTimeout(resolve, 300));

    const threads: Record<string, ConversationThread> = {
        c1: {
            id: 'c1',
            participant: 'Dr. Jean Dupont',
            messages: [
                { id: 'm1', contenu: 'Bonjour, j\'ai reçu mes résultats.', date: '2026-08-25 08:00', emetteur: 'moi' },
                { id: 'm2', contenu: 'Bonjour, tout est normal.', date: '2026-08-25 08:05', emetteur: 'autre' },
                { id: 'm3', contenu: 'Vos résultats sont bons.', date: '2026-08-25 08:12', emetteur: 'autre' },
            ],
        },
        c2: {
            id: 'c2',
            participant: 'Nutritionniste Sarah K.',
            messages: [
                { id: 'm4', contenu: 'Bonjour, je voudrais un conseil.', date: '2026-08-24 16:40', emetteur: 'moi' },
                { id: 'm5', contenu: 'Pensez à suivre le plan.', date: '2026-08-24 16:45', emetteur: 'autre' },
            ],
        },
        c3: {
            id: 'c3',
            participant: 'Équipe de soins',
            messages: [
                { id: 'm6', contenu: 'Votre prochain rendez-vous est confirmé.', date: '2026-08-23 09:30', emetteur: 'autre' },
            ],
        },
    };

    return threads[id] || threads['c1'];
}
