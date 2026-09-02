import { useRef, useState } from 'react';

// Icônes SVG (déplacées ici pour l'autonomie du composant)
const ClipIcon = () => (
    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
    </svg>
);

const CameraIcon = () => (
    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
        <circle cx="12" cy="13" r="4"></circle>
    </svg>
);

const MicIcon = () => (
    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
        <line x1="12" y1="19" x2="12" y2="23"></line>
        <line x1="8" y1="23" x2="16" y2="23"></line>
    </svg>
);

const SendIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
        <line x1="22" y1="2" x2="11" y2="13"></line>
        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
    </svg>
);

interface MessageComposerProps {
    onSendMessage: (contenu: string, media?: File) => Promise<void>;
}

export function MessageComposer({ onSendMessage }: MessageComposerProps) {
    const [text, setText] = useState('');
    const [isRecording, setIsRecording] = useState(false);
    const [isSending, setIsSending] = useState(false);

    const fileInputRef = useRef<HTMLInputElement>(null);
    const photoInputRef = useRef<HTMLInputElement>(null);
    const recorderRef = useRef<MediaRecorder | null>(null);
    const audioChunksRef = useRef<Blob[]>([]);

    const send = async (content: string, media?: File) => {
        if ((!content.trim() && !media) || isSending) return;
        setIsSending(true);
        try {
            await onSendMessage(content, media);
            if (!media) setText('');
        } finally {
            setIsSending(false);
        }
    };

    const handleSend = () => {
        void send(text.trim());
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) void send(file.name, file);
        e.target.value = '';
    };

    const handleMicClick = async () => {
        if (isRecording) {
            recorderRef.current?.stop();
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const recorder = new MediaRecorder(stream);
            recorderRef.current = recorder;
            audioChunksRef.current = [];
            recorder.ondataavailable = (event) => {
                if (event.data.size > 0) audioChunksRef.current.push(event.data);
            };
            recorder.onstop = () => {
                stream.getTracks().forEach((track) => track.stop());
                const mimeType = recorder.mimeType || 'audio/webm';
                const audio = new File([new Blob(audioChunksRef.current, { type: mimeType })], `message-vocal-${Date.now()}.webm`, { type: mimeType });
                setIsRecording(false);
                void send('Message vocal', audio);
            };
            recorder.start();
            setIsRecording(true);
        } catch {
            setIsRecording(false);
            window.alert('L’accès au microphone est nécessaire pour enregistrer un message vocal.');
        }
    };

    return (
        <>
            {/* Inputs masqués pour les fichiers */}
            <input
                type="file"
                ref={fileInputRef}
                style={{ display: 'none' }}
                onChange={handleFileChange}
            />
            <input
                type="file"
                accept="image/*"
                ref={photoInputRef}
                style={{ display: 'none' }}
                onChange={handleFileChange}
            />

            {/* Zone de saisie */}
            <div className="message-thread__footer">
                <button
                    type="button"
                    className="icon-button"
                    title="Joindre un fichier"
                    onClick={() => fileInputRef.current?.click()}
                >
                    <ClipIcon />
                </button>

                <div className="message-thread__input-container">
                    <input
                        type="text"
                        placeholder={isRecording ? 'Enregistrement en cours...' : 'Écrivez un message...'}
                        value={text}
                        disabled={isRecording || isSending}
                        onChange={(e) => setText(e.target.value)}
                        onKeyDown={handleKeyDown}
                    />
                    <button
                        type="button"
                        className="icon-button"
                        title="Envoyer une photo"
                        onClick={() => photoInputRef.current?.click()}
                    >
                        <CameraIcon />
                    </button>
                </div>

                {text.trim().length > 0 ? (
                    <button
                        type="button"
                        className="icon-button icon-button--send"
                        title="Envoyer"
                        onClick={handleSend}
                        disabled={isSending}
                    >
                        <SendIcon />
                    </button>
                ) : (
                    <button
                        type="button"
                        className={`icon-button ${isRecording ? 'icon-button--recording' : ''}`}
                        title="Message vocal"
                        onClick={handleMicClick}
                        disabled={isSending}
                    >
                        <MicIcon />
                    </button>
                )}
            </div>
        </>
    );
}
