import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Stepper } from '@/react/components/Navigation/Stepper';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateProfessional } from '@/react/features/admin/professionals/hooks/useCreateProfessional';
import { ProfessionalFormFields } from "@/react/features/root/users/components/ProfessionalFormFields";
import { AddressFields } from "@/react/features/root/users/components/AddressFields";
import { AvatarUpload } from "@/react/features/root/users/components/AvatarUpload";
import { useI18n } from '@/react/i18n/I18nContext';

interface ProfessionalFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess?: () => void;
}

export function ProfessionalFormModal({ isOpen, onClose, onSuccess }: ProfessionalFormModalProps) {
    const { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error } =
        useCreateProfessional();
    const [step, setStep] = useState(0);
    const { t } = useI18n();

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
        const success = await submit(); // submit sans argument
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    const renderStepContent = () => {
        switch (step) {
            case 0:
                return <ProfessionalFormFields form={form} updateField={updateField} />;
            case 1:
                return <AddressFields address={form.address} onChange={updateAddress} />;
            case 2:
                return (
                    <AvatarUpload
                        value={form.avatarUrl}
                        name={form.fullName}
                        onChange={updateAvatar}
                    />
                );
            case 3:
                return (
                    <div className="professional-form-modal__summary">
                        <h3>{t('Vérifiez les informations')}</h3>
                        <p><strong>{t('Nom complet :')}</strong> {form.fullName}</p>
                        <p><strong>{t('Email :')}</strong> {form.email}</p>
                        <p><strong>{t('Téléphone :')}</strong> {form.phone || '—'}</p>
                        <p><strong>{t('Genre :')}</strong> {form.gender}</p>
                        <p><strong>{t('Licence :')}</strong> {form.licenseNumber}</p>
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
                <h2>{t('Ajouter un professionnel')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Stepper
                    steps={steps}
                    activeStepId={activeStepId}
                    onStepChange={handleStepChange}
                />
                <div className="professional-form-modal__content">
                    {renderStepContent()}
                </div>
                <div className="professional-form-modal__footer">
                    {step > 0 && (
                        <Button variant="outline" onClick={handlePrev}>
                            {t('Précédent')}
                        </Button>
                    )}
                    {step < steps.length - 1 ? (
                        <Button variant="primary" onClick={handleNext}>
                            {t('Suivant')}
                        </Button>
                    ) : (
                        <Button variant="primary" onClick={handleSubmit} disabled={isSubmitting}>
                            {isSubmitting ? t('Création...') : t('Créer')}
                        </Button>
                    )}
                </div>
            </div>
        </Modal>
    );
}
