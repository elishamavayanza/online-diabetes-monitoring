import React, {useEffect, useState} from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Stepper } from '@/react/components/Navigation/Stepper';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateProfessional } from '@/react/features/admin/professionals/hooks/useUpdateProfessional';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { AddressFields } from "@/react/features/root/users/components/AddressFields";
import { AvatarUpload } from "@/react/features/root/users/components/AvatarUpload";
import { ProfessionalFormValues } from "@/react/features/root/users/types/userForm.types";
import { useI18n } from '@/react/i18n/I18nContext';

interface ProfessionalEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    professionalId: string;
    professionalData: ProfessionalFormValues;
    onSuccess?: () => void;
}

export function ProfessionalEditModal({
                                          isOpen,
                                          onClose,
                                          professionalId,
                                          professionalData,
                                          onSuccess,
                                      }: ProfessionalEditModalProps) {
    const { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error } =
        useUpdateProfessional(professionalData, professionalId);
    const [step, setStep] = useState(0);
    const { t } = useI18n();

    useEffect(() => {
        if (isOpen) {
            setStep(0);
        }
    }, [isOpen]);

    const steps = [
        { id: 'infos', label: t('Informations') },
        { id: 'address', label: t('Adresse') },
        { id: 'photo', label: t('Photo') },
        { id: 'summary', label: t('Récapitulatif') },
    ];

    const activeStepId = steps[step].id;

    const handleStepChange = (newStepId: string) => {
        const index = steps.findIndex((s) => s.id === newStepId);
        if (index >= 0) setStep(index);
    };

    const handleNext = () => setStep((prev) => Math.min(prev + 1, steps.length - 1));
    const handlePrev = () => setStep((prev) => Math.max(prev - 1, 0));

    const handleSubmit = async () => {
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    // Contenu de l'étape "Informations" sans email ni password
    const renderInfoFields = () => (
        <>
            <FormField label={t('Nom complet *')}>
                <Input
                    value={form.fullName}
                    onChange={(e) => updateField('fullName', e.target.value)}
                    required
                />
            </FormField>
            <FormField label={t('Téléphone')}>
                <Input
                    value={form.phone}
                    onChange={(e) => updateField('phone', e.target.value)}
                />
            </FormField>
            <FormField label={t('Genre *')}>
                <Select
                    value={form.gender}
                    onChange={(e) => updateField('gender', e.target.value)}
                    options={[
                        { value: 'MALE', label: t('Masculin') },
                        { value: 'FEMALE', label: t('Féminin') },
                        { value: 'OTHER', label: t('Autre') },
                        { value: 'UNSPECIFIED', label: t('Non spécifié') },
                    ]}
                />
            </FormField>
            <FormField label={t('Numéro de licence *')}>
                <Input
                    value={form.licenseNumber}
                    onChange={(e) => updateField('licenseNumber', e.target.value)}
                    required
                />
            </FormField>
            <FormField label={t('Type professionnel *')}>
                <Select
                    value={form.professionalType}
                    onChange={(e) => updateField('professionalType', e.target.value)}
                    options={[
                        { value: 'CLINICIAN', label: t('Clinicien') },
                        { value: 'NUTRITIONIST', label: t('Nutritionniste') },
                    ]}
                />
            </FormField>
            <FormField label={t('Spécialité')}>
                <Input
                    value={form.specialty}
                    onChange={(e) => updateField('specialty', e.target.value)}
                />
            </FormField>
            <FormField label={t('URL de signature')}>
                <Input
                    value={form.signatureUrl}
                    onChange={(e) => updateField('signatureUrl', e.target.value)}
                />
            </FormField>
        </>
    );

    const renderStepContent = () => {
        switch (step) {
            case 0:
                return renderInfoFields();
            case 1:
                return <AddressFields address={form.address} onChange={updateAddress} />;
            case 2:
                return <AvatarUpload value={form.avatarUrl} name={form.fullName} onChange={updateAvatar} />;
            case 3:
                return (
                    <div className="professional-form-modal__summary">
                        <h3>{t('Vérifiez les informations')}</h3>
                        <p><strong>{t('Nom complet :')}</strong> {form.fullName}</p>
                        <p><strong>{t('Téléphone :')}</strong> {form.phone || '—'}</p>
                        <p><strong>{t('Genre :')}</strong> {form.gender}</p>
                        <p><strong>{t('Licence :')}</strong> {form.licenseNumber}</p>
                        <p><strong>{t('Type :')}</strong> {form.professionalType}</p>
                        <p><strong>{t('Spécialité :')}</strong> {form.specialty || '—'}</p>
                        <p><strong>{t('Adresse :')}</strong> {form.address.street} {form.address.city} {form.address.postalCode} {form.address.country}</p>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="large">
            <div className="professional-form-modal">
                <h2>{t('Modifier le professionnel')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Stepper steps={steps} activeStepId={activeStepId} onStepChange={handleStepChange} />
                <div className="professional-form-modal__content">{renderStepContent()}</div>
                <div className="professional-form-modal__footer">
                    {step > 0 && <Button variant="outline" onClick={handlePrev}>{t('Précédent')}</Button>}
                    {step < steps.length - 1 ? (
                        <Button variant="primary" onClick={handleNext}>{t('Suivant')}</Button>
                    ) : (
                        <Button variant="primary" onClick={handleSubmit} disabled={isSubmitting}>
                            {isSubmitting ? t('Enregistrement...') : t('Enregistrer')}
                        </Button>
                    )}
                </div>
            </div>
        </Modal>
    );
}
