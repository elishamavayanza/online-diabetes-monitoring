import React, { useRef, useState } from 'react';
import { Avatar } from '@/react/components/UI/Avatar';
import { Modal } from '@/react/components/UI/Modal';
import { ImageEditor } from '@/react/components/UI/ImageEditor/ImageEditor';

interface AvatarUploadProps {
    value?: string;
    name?: string;
    size?: number;
    onChange: (url: string, file: File | null) => void;
}

export function AvatarUpload({ value, name = 'Utilisateur', size = 80, onChange }: AvatarUploadProps) {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [preview, setPreview] = useState<string | undefined>(value);
    const [editorOpen, setEditorOpen] = useState(false);
    const [tempImage, setTempImage] = useState<string>('');

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            const url = URL.createObjectURL(file);
            setTempImage(url);
            setEditorOpen(true);
        }
    };

    const handleRemove = () => {
        setPreview(undefined);
        onChange('', null);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    // Accepter File | undefined pour correspondre à ImageEditor
    const handleEditorApply = (url: string, file?: File) => {
        setPreview(url);
        onChange(url, file ?? null); // convertir undefined en null
        setEditorOpen(false);
    };

    return (
        <div className="avatar-upload">
            <div className="avatar-upload__preview" style={{ width: size, height: size }}>
                <Avatar src={preview} name={name} size="large" shape="circle" />
                <button
                    type="button"
                    className="avatar-upload__edit"
                    onClick={() => fileInputRef.current?.click()}
                    aria-label="Changer la photo"
                >
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                    </svg>
                </button>
            </div>
            <input
                ref={fileInputRef}
                type="file"
                accept="image/jpeg,image/png,image/webp"
                style={{ display: 'none' }}
                onChange={handleFileChange}
            />
            {preview && (
                <button type="button" className="avatar-upload__remove" onClick={handleRemove}>
                    Supprimer
                </button>
            )}

            {editorOpen && (
                <Modal isOpen={editorOpen} onClose={() => setEditorOpen(false)} size="small">
                    <ImageEditor
                        src={tempImage}
                        onCancel={() => setEditorOpen(false)}
                        onApply={handleEditorApply}
                    />
                </Modal>
            )}
        </div>
    );
}
