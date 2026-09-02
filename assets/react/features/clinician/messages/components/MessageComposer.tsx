import { useRef, useState } from 'react';
import { ClipIcon, CameraIcon, SendIcon, MicIcon } from './MessageIcons';

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
                const audioBlob = new Blob(audioChunksRef.current, { type: mimeType });
                const audioFile = new File([audioBlob], `vocal-${Date.now()}.webm`, { type: mimeType });

                setIsRecording(false);
                // On passe une chaîne vide comme contenu pour qu'il n'y ait pas de texte "Message vocal" indésirable dans la bulle
                void send('', audioFile);
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
            <input type="file" ref={fileInputRef} style={{ display: 'none' }} onChange={handleFileChange} />
            <input type="file" accept="image/*" ref={photoInputRef} style={{ display: 'none' }} onChange={handleFileChange} />

            <div className="message-thread__footer">
                <button type="button" className="icon-button" title="Joindre un fichier" onClick={() => fileInputRef.current?.click()}>
                    <ClipIcon />
                </button>

                <div className="message-thread__input-container">
                    <input
                        type="text"
                        placeholder={isRecording ? 'Enregistrement du message vocal...' : 'Écrivez un message...'}
                        value={text}
                        disabled={isRecording || isSending}
                        onChange={(e) => setText(e.target.value)}
                        onKeyDown={handleKeyDown}
                    />
                    <button type="button" className="icon-button" title="Envoyer une photo" onClick={() => photoInputRef.current?.click()}>
                        <CameraIcon />
                    </button>
                </div>

                {text.trim().length > 0 ? (
                    <button type="button" className="icon-button icon-button--send" title="Envoyer" onClick={handleSend} disabled={isSending}>
                        <SendIcon />
                    </button>
                ) : (
                    <button type="button" className={`icon-button ${isRecording ? 'icon-button--recording' : ''}`} title="Message vocal" onClick={handleMicClick} disabled={isSending}>
                        <MicIcon />
                    </button>
                )}
            </div>
        </>
    );
}
