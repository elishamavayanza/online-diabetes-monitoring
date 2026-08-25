export interface Conversation {
    id: string;
    participant: string;
    type: 'Patient' | 'Professionnel' | 'Membre';
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
