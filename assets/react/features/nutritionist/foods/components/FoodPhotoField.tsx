import { useState } from 'react';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { ImageEditor } from '@/react/components/UI/ImageEditor/ImageEditor';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';

interface FoodPhotoFieldProps {
    photoUrl: string;
    onPhotoUrlChange: (url: string) => void;
    onPhotoFileChange: (file: File | null) => void;
}

export function FoodPhotoField({ photoUrl, onPhotoUrlChange, onPhotoFileChange }: FoodPhotoFieldProps) {
    const [preview, setPreview] = useState<string | null>(photoUrl || null);
    const [isEditorOpen, setIsEditorOpen] = useState(false);

    const handleFilesSelected = (files: File[]) => {
        if (files.length === 0) return;
        const file = files[0];
        onPhotoFileChange(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            const dataUrl = e.target?.result as string;
            setPreview(dataUrl);
        };
        reader.readAsDataURL(file);
    };

    const handleApplyEditedImage = (_result: string, file?: File) => {
        if (file) {
            onPhotoFileChange(file);
            const reader = new FileReader();
            reader.onload = (e) => setPreview(e.target?.result as string);
            reader.readAsDataURL(file);
        }
        setIsEditorOpen(false);
    };

    const handleUrlChange = (url: string) => {
        onPhotoUrlChange(url);
        setPreview(url || null);
        onPhotoFileChange(null);
    };

    return (
        <>
            <FormField label="Photo de l'aliment">
                <div className="food-photo-field">
                    <FileUpload
                        accept="image/*"
                        multiple={false}
                        maxFiles={1}
                        maxSizeInMB={5}
                        label="Cliquez ou déposez une image ici"
                        hint="PNG ou JPG, max 5 Mo"
                        onFilesSelected={handleFilesSelected}
                    />
                    {preview && (
                        <div className="food-photo-field__preview">
                            <img src={preview} alt="Aperçu de l'aliment" />
                            <Button type="button" variant="outline" onClick={() => setIsEditorOpen(true)}>
                                Recadrer
                            </Button>
                        </div>
                    )}
                    <FormField label="Ou URL de l'image">
                        <Input
                            type="url"
                            value={photoUrl}
                            onChange={(e) => handleUrlChange(e.target.value)}
                            placeholder="https://exemple.com/pomme.jpg"
                        />
                    </FormField>
                </div>
            </FormField>

            {isEditorOpen && preview && (
                <Modal isOpen={isEditorOpen} onClose={() => setIsEditorOpen(false)}>
                    <ImageEditor
                        src={preview}
                        onCancel={() => setIsEditorOpen(false)}
                        onApply={handleApplyEditedImage}
                        aspect={1}
                        outputSize={400}
                    />
                </Modal>
            )}
        </>
    );
}
