import { Conversation, ConversationThread } from '../types';

export async function fetchConversations(): Promise<Conversation[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'c1',
            participant: 'Marie Zawadi',
            type: 'Patient',
            dernierMessage: 'Merci pour le plan, je le suis.',
            dateDernierMessage: '2026-08-25 08:12',
            nonLus: 2,
        },
        {
            id: 'c2',
            participant: 'Dr. Jean Dupont',
            type: 'Professionnel',
            dernierMessage: 'Pouvez-vous vérifier le régime ?',
            dateDernierMessage: '2026-08-24 16:45',
            nonLus: 0,
        },
        {
            id: 'c3',
            participant: 'Équipe nutrition',
            type: 'Membre',
            dernierMessage: 'Réunion demain.',
            dateDernierMessage: '2026-08-24 09:30',
            nonLus: 1,
        },
    ];
}

export async function fetchConversationThread(id: string): Promise<ConversationThread> {
    await new Promise((resolve) => setTimeout(resolve, 300));

    const threads: Record<string, ConversationThread> = {
        c1: {
            id: 'c1',
            participant: 'Marie Zawadi',
            messages: [
                { id: 'm1', contenu: 'Bonjour, j\'ai des questions sur le plan.', date: '2026-08-25 08:00', emetteur: 'autre' },
                { id: 'm2', contenu: 'Bonjour, je vous écoute.', date: '2026-08-25 08:05', emetteur: 'moi' },
                { id: 'm3', contenu: 'Merci pour le plan, je le suis.', date: '2026-08-25 08:12', emetteur: 'autre' },
            ],
        },
        c2: {
            id: 'c2',
            participant: 'Dr. Jean Dupont',
            messages: [
                { id: 'm4', contenu: 'Bonjour, le patient a des restrictions.', date: '2026-08-24 16:40', emetteur: 'autre' },
                { id: 'm5', contenu: 'Je vais ajuster le plan.', date: '2026-08-24 16:45', emetteur: 'moi' },
            ],
        },
        c3: {
            id: 'c3',
            participant: 'Équipe nutrition',
            messages: [
                { id: 'm6', contenu: 'Pensez à remplir les comptes-rendus.', date: '2026-08-24 09:00', emetteur: 'autre' },
            ],
        },
    };

    return threads[id] || threads['c1'];
}
