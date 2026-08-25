export interface Conversation {
    id: string;
    participant: string;
    type: 'Médecin' | 'Nutritionniste' | 'Équipe' | 'Organisation';
    dernierMessage: string;
    dateDernierMessage: string;
    nonLus: number;
}

export interface Message {
    id: string;
    contenu: string;
    date: string;
    emetteur: 'moi' | 'autre';
}

export interface ConversationThread {
    id: string;
    participant: string;
    messages: Message[];
}
