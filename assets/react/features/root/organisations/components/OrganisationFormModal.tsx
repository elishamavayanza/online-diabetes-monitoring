import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { useCreateOrganisation } from '../hooks/useCreateOrganisation';
import { ImageEditor } from '@/react/components/UI/ImageEditor/ImageEditor';

interface OrganisationFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: () => void;
}

export function OrganisationFormModal({ isOpen, onClose, onSuccess }: OrganisationFormModalProps) {
    const { form, updateField, updateAddress, submit, isSubmitting, error, setLogo } = useCreateOrganisation();

    // États pour le logo
    const [logoPreview, setLogoPreview] = useState<string | null>(null);
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [isEditorOpen, setIsEditorOpen] = useState(false);

    const typeOptions = [
        { value: 'HOSPITAL', label: 'Hôpital' },
        { value: 'CLINIC', label: 'Clinique' },
        { value: 'NETWORK', label: 'Réseau' },
    ];

    const handleFilesSelected = (files: File[]) => {
        if (files.length > 0) {
            const file = files[0];
            setLogoFile(file);
            setLogo(file); // important pour l'upload
            const reader = new FileReader();
            reader.onload = (e) => {
                const dataUrl = e.target?.result as string;
                setLogoPreview(dataUrl);
                updateField('logoUrl', dataUrl);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleApplyEditedImage = (result: string, file?: File) => {
        setLogoPreview(result);
        updateField('logoUrl', result);
        if (file) {
            setLogoFile(file);
            setLogo(file);
        }
        setIsEditorOpen(false);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="organisation-form-modal">
                <h2>Ajouter une organisation</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label="Nom complet *">
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            placeholder="DiabCare Health Group"
                            required
                        />
                    </FormField>
                    <FormField label="Nom court">
                        <Input
                            value={form.shortName}
                            onChange={(e) => updateField('shortName', e.target.value)}
                            placeholder="DHG"
                        />
                    </FormField>
                    <FormField label="Type *">
                        <Select
                            value={form.type}
                            onChange={(e) => updateField('type', e.target.value as any)}
                            options={typeOptions}
                        />
                    </FormField>
                    <FormField label="Email">
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                            placeholder="contact@diabcare.com"
                        />
                    </FormField>
                    <FormField label="Téléphone">
                        <Input
                            value={form.phone}
                            onChange={(e) => updateField('phone', e.target.value)}
                            placeholder="+243990000000"
                        />
                    </FormField>
                    <FormField label="Site Web">
                        <Input
                            value={form.website}
                            onChange={(e) => updateField('website', e.target.value)}
                            placeholder="https://www.diabcare.com"
                        />
                    </FormField>

                    <FormField label="Logo de l'organisation">
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                            <FileUpload
                                accept="image/*"
                                multiple={false}
                                maxFiles={1}
                                maxSizeInMB={5}
                                label="Cliquez ou déposez le logo ici"
                                hint="PNG, JPG ou SVG recommandé"
                                onFilesSelected={handleFilesSelected}
                            />
                            {logoPreview && (
                                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                                    <img
                                        src={logoPreview}
                                        alt="Aperçu du logo"
                                        style={{ width: 60, height: 60, borderRadius: '50%', objectFit: 'cover' }}
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setIsEditorOpen(true)}
                                    >
                                        Éditer l'image
                                    </Button>
                                </div>
                            )}
                        </div>
                    </FormField>

                    <div className="organisation-form-modal__address">
                        <FormField label="Rue">
                            <Input
                                value={form.address?.street ?? ''}
                                onChange={(e) => updateAddress('street', e.target.value)}
                            />
                        </FormField>
                        <FormField label="Ville">
                            <Input
                                value={form.address?.city ?? ''}
                                onChange={(e) => updateAddress('city', e.target.value)}
                            />
                        </FormField>
                        <FormField label="Code postal">
                            <Input
                                value={form.address?.postalCode ?? ''}
                                onChange={(e) => updateAddress('postalCode', e.target.value)}
                            />
                        </FormField>
                        <FormField label="Pays">
                            <Input
                                value={form.address?.country ?? ''}
                                onChange={(e) => updateAddress('country', e.target.value)}
                            />
                        </FormField>
                    </div>

                    <FormField label="Actif">
                        <Switch
                            checked={form.active}
                            onChange={(e) => updateField('active', e.target.checked)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Création...' : 'Créer'}
                        </Button>
                    </div>
                </Form>
            </div>

            {isEditorOpen && logoPreview && (
                <Modal isOpen={isEditorOpen} onClose={() => setIsEditorOpen(false)}>
                    <ImageEditor
                        src={logoPreview}
                        onCancel={() => setIsEditorOpen(false)}
                        onApply={handleApplyEditedImage}
                        aspect={1}
                        outputSize={300}
                    />
                </Modal>
            )}
        </Modal>
    );
}
