import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Stepper } from '@/react/components/Navigation/Stepper';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUpdateProfessional } from '@/react/features/root/users/hooks/useUpdateProfessional';
import { ProfessionalFormFields } from "@/react/features/root/users/components/ProfessionalFormFields";
import { AddressFields } from "@/react/features/root/users/components/AddressFields";
import { AvatarUpload } from "@/react/features/root/users/components/AvatarUpload";
import { ProfessionalFormValues } from "@/react/features/root/users/types/userForm.types";

interface ProfessionalEditModalProps {
    isOpen: boolean;
    onClose: () => void;
    professionalId: string;                         // ✅ ID séparé
    professionalData: ProfessionalFormValues;      // données pré-remplies
}

export function ProfessionalEditModal({
                                          isOpen,
                                          onClose,
                                          professionalId,
                                          professionalData,
                                      }: ProfessionalEditModalProps) {
    const { form, updateField, updateAddress, updateAvatar, submit, isSubmitting, error } =
        useUpdateProfessional(professionalData);
    const [step, setStep] = useState(0);

    const steps = [
        { id: 'infos', label: 'Informations' },
        { id: 'address', label: 'Adresse' },
        { id: 'photo', label: 'Photo' },
        { id: 'summary', label: 'Récapitulatif' },
    ];

    const activeStepId = steps[step].id;

    const handleStepChange = (newStepId: string) => {
        const index = steps.findIndex((s) => s.id === newStepId);
        if (index >= 0) setStep(index);
    };

    const handleNext = () => setStep((prev) => Math.min(prev + 1, steps.length - 1));
    const handlePrev = () => setStep((prev) => Math.max(prev - 1, 0));

    const renderStepContent = () => {
        switch (step) {
            case 0:
                return <ProfessionalFormFields form={form} updateField={updateField} />;
            case 1:
                return <AddressFields address={form.address} onChange={updateAddress} />;
            case 2:
                return <AvatarUpload value={form.avatarUrl} name={form.fullName} onChange={updateAvatar} />;
            case 3:
                return (
                    <div className="professional-form-modal__summary">
                        <h3>Vérifiez les informations</h3>
                        <p><strong>Nom complet :</strong> {form.fullName}</p>
                        <p><strong>Email :</strong> {form.email}</p>
                        <p><strong>Téléphone :</strong> {form.phone || '—'}</p>
                        <p><strong>Genre :</strong> {form.gender}</p>
                        <p><strong>Licence :</strong> {form.licenseNumber}</p>
                        <p><strong>Spécialité :</strong> {form.specialty || '—'}</p>
                        <p><strong>Adresse :</strong> {form.address.street} {form.address.city} {form.address.postalCode} {form.address.country}</p>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="large">
            <div className="professional-form-modal">
                <h2>Modifier le professionnel</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Stepper steps={steps} activeStepId={activeStepId} onStepChange={handleStepChange} />
                <div className="professional-form-modal__content">{renderStepContent()}</div>
                <div className="professional-form-modal__footer">
                    {step > 0 && <Button variant="outline" onClick={handlePrev}>Précédent</Button>}
                    {step < steps.length - 1 ? (
                        <Button variant="primary" onClick={handleNext}>Suivant</Button>
                    ) : (
                        <Button variant="primary" onClick={() => submit(professionalId)} disabled={isSubmitting}>
                            {isSubmitting ? 'Enregistrement...' : 'Enregistrer'}
                        </Button>
                    )}
                </div>
            </div>
        </Modal>
    );
}
