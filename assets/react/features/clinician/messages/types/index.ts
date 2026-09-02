export interface Conversation {
    id: string;
    participant: string;
    type: 'Patient' | 'Professionnel' | 'Membre';
    participantId?: string;
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
    status?: 'sent' | 'read';   // ✅ ajout

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
    participantId: string;
    messages: Message[];
}
