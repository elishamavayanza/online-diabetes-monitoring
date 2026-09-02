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
    attachments?: MessageAttachment[];
}

export interface MessageAttachment {
    id: string;
    fileUrl: string;
    fileName: string;
    mimeType: string;
}

export interface ConversationThread {
    id: string;
    participant: string;
    messages: Message[];
}
