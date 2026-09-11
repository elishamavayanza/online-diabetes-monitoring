import React, { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { ImageEditor } from '@/react/components/UI/ImageEditor/ImageEditor';
import { useUpdateOrganisation } from '../hooks/useUpdateOrganisation';
import { CreateOrganisationPayload } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface OrganisationEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    organisationId: string;
    organisationData: CreateOrganisationPayload;
    onSuccess?: () => void;
}

export function OrganisationEditModal({
                                          isOpen,
                                          onClose,
                                          organisationId,
                                          organisationData,
                                          onSuccess,
                                      }: OrganisationEditModalProps) {
    const { t } = useI18n();
    const { form, updateField, updateAddress, submit, isSubmitting, error, setLogo } =
        useUpdateOrganisation(organisationId, organisationData);

    const [logoPreview, setLogoPreview] = useState<string | null>(organisationData.logoUrl || null);
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [isEditorOpen, setIsEditorOpen] = useState(false);

    useEffect(() => {
        setLogoPreview(organisationData.logoUrl || null);
    }, [organisationData]);

    const typeOptions = [
        { value: 'HOSPITAL', label: t('H\u00f4pital') },
        { value: 'CLINIC', label: t('Clinique') },
        { value: 'NETWORK', label: t('R\u00e9seau') },
    ];

    const handleFilesSelected = (files: File[]) => {
        if (files.length > 0) {
            const file = files[0];
            setLogoFile(file);
            setLogo(file);
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
                <h2>{t("Modifier l\u2019organisation")}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Nom complet *')}>
                        <Input
                            value={form.name}
                            onChange={(e) => updateField('name', e.target.value)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Nom court')}>
                        <Input
                            value={form.shortName}
                            onChange={(e) => updateField('shortName', e.target.value)}
                        />
                    </FormField>
                    <FormField label={t('Type *')}>
                        <Select
                            value={form.type}
                            onChange={(e) => updateField('type', e.target.value as any)}
                            options={typeOptions}
                        />
                    </FormField>
                    <FormField label={t('Email')}>
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                        />
                    </FormField>
                    <FormField label={t('T\u00e9l\u00e9phone')}>
                        <Input
                            value={form.phone}
                            onChange={(e) => updateField('phone', e.target.value)}
                        />
                    </FormField>
                    <FormField label={t('Site Web')}>
                        <Input
                            value={form.website}
                            onChange={(e) => updateField('website', e.target.value)}
                        />
                    </FormField>

                    <FormField label={t("Logo de l\u2019organisation")}>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                            <FileUpload
                                accept="image/*"
                                multiple={false}
                                maxFiles={1}
                                maxSizeInMB={5}
                                label={t('Cliquez ou d\u00e9posez le logo ici')}
                                hint={t('PNG, JPG ou SVG recommand\u00e9')}
                                onFilesSelected={handleFilesSelected}
                            />
                            {logoPreview && (
                                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                                    <img
                                        src={logoPreview}
                                        alt={t('Aper\u00e7u du logo')}
                                        style={{ width: 60, height: 60, borderRadius: '50%', objectFit: 'cover' }}
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setIsEditorOpen(true)}
                                    >
                                        {t("\u00c9diter l\u2019image")}
                                    </Button>
                                </div>
                            )}
                        </div>
                    </FormField>

                    <div className="organisation-form-modal__address">
                        <FormField label={t('Rue')}>
                            <Input value={form.address?.street ?? ''} onChange={(e) => updateAddress('street', e.target.value)} />
                        </FormField>
                        <FormField label={t('Ville')}>
                            <Input value={form.address?.city ?? ''} onChange={(e) => updateAddress('city', e.target.value)} />
                        </FormField>
                        <FormField label={t('Code postal')}>
                            <Input value={form.address?.postalCode ?? ''} onChange={(e) => updateAddress('postalCode', e.target.value)} />
                        </FormField>
                        <FormField label={t('Pays')}>
                            <Input value={form.address?.country ?? ''} onChange={(e) => updateAddress('country', e.target.value)} />
                        </FormField>
                    </div>

                    <FormField label={t('Actif')}>
                        <Switch
                            checked={form.active}
                            onChange={(e) => updateField('active', e.target.checked)}
                        />
                    </FormField>

                    <div className="organisation-form-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>{isSubmitting ? t('Mise \u00e0 jour...') : t('Enregistrer')}</Button>
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
