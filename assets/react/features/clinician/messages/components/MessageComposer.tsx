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
    const textareaRef = useRef<HTMLTextAreaElement>(null);

    // Ajuste automatiquement la hauteur du textarea en fonction du contenu
    const autoResize = () => {
        const textarea = textareaRef.current;
        if (!textarea) return;
        textarea.style.height = 'auto';
        textarea.style.height = `${Math.min(textarea.scrollHeight, 150)}px`; // hauteur max 150px
    };

    const send = async (content: string, media?: File) => {
        if ((!content.trim() && !media) || isSending) return;
        setIsSending(true);
        try {
            await onSendMessage(content, media);
            if (!media) {
                setText('');
                // Réinitialiser la hauteur
                if (textareaRef.current) {
                    textareaRef.current.style.height = 'auto';
                }
            }
        } finally {
            setIsSending(false);
        }
    };

    const handleSend = () => {
        void send(text.trim());
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        setText(e.target.value);
        autoResize();
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
                    <textarea
                        ref={textareaRef}
                        rows={1}
                        placeholder={isRecording ? 'Enregistrement du message vocal...' : 'Écrivez un message...'}
                        value={text}
                        disabled={isRecording || isSending}
                        onChange={handleChange}
                        onKeyDown={handleKeyDown}
                        className="message-thread__textarea"
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
