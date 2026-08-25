import { Conversation, ConversationThread } from '../types';

export async function fetchConversations(): Promise<Conversation[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'c1',
            participant: 'Marie Zawadi',
            type: 'Patient',
            dernierMessage: 'Merci docteur, je suivrai vos conseils.',
            dateDernierMessage: '2026-08-25 08:12',
            nonLus: 2,
        },
        {
            id: 'c2',
            participant: 'Nutritionniste Sarah K.',
            type: 'Professionnel',
            dernierMessage: 'Pouvez-vous vérifier le plan alimentaire ?',
            dateDernierMessage: '2026-08-24 16:45',
            nonLus: 0,
        },
        {
            id: 'c3',
            participant: 'Membre équipe Diabétologie',
            type: 'Membre',
            dernierMessage: 'Réunion demain à 9h.',
            dateDernierMessage: '2026-08-24 09:30',
            nonLus: 1,
        },
    ];
}

export async function fetchConversationThread(id: string): Promise<ConversationThread> {
    await new Promise((resolve) => setTimeout(resolve, 300));

    // Simulation de thread selon la conversation sélectionnée
    const threads: Record<string, ConversationThread> = {
        c1: {
            id: 'c1',
            participant: 'Marie Zawadi',
            messages: [
                { id: 'm1', contenu: 'Bonjour docteur, j\'ai des questions sur mon traitement.', date: '2026-08-25 08:00', emetteur: 'autre' },
                { id: 'm2', contenu: 'Bonjour Marie, je vous écoute.', date: '2026-08-25 08:05', emetteur: 'moi' },
                { id: 'm3', contenu: 'Merci docteur, je suivrai vos conseils.', date: '2026-08-25 08:12', emetteur: 'autre' },
            ],
        },
        c2: {
            id: 'c2',
            participant: 'Nutritionniste Sarah K.',
            messages: [
                { id: 'm4', contenu: 'Bonjour, le plan de Jean est prêt.', date: '2026-08-24 16:40', emetteur: 'autre' },
                { id: 'm5', contenu: 'Merci, je vais vérifier.', date: '2026-08-24 16:45', emetteur: 'moi' },
            ],
        },
        c3: {
            id: 'c3',
            participant: 'Membre équipe Diabétologie',
            messages: [
                { id: 'm6', contenu: 'Pensez à remplir le compte-rendu.', date: '2026-08-24 09:00', emetteur: 'autre' },
                { id: 'm7', contenu: 'Je le ferai avant la réunion.', date: '2026-08-24 09:30', emetteur: 'moi' },
            ],
        },
    };

    return threads[id] || threads['c1'];
}
