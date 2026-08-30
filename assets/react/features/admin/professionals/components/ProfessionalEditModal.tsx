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

    useEffect(() => {
        if (isOpen) {
            setStep(0);
        }
    }, [isOpen]);

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
            <FormField label="Nom complet *">
                <Input
                    value={form.fullName}
                    onChange={(e) => updateField('fullName', e.target.value)}
                    required
                />
            </FormField>
            <FormField label="Téléphone">
                <Input
                    value={form.phone}
                    onChange={(e) => updateField('phone', e.target.value)}
                />
            </FormField>
            <FormField label="Genre *">
                <Select
                    value={form.gender}
                    onChange={(e) => updateField('gender', e.target.value)}
                    options={[
                        { value: 'MALE', label: 'Masculin' },
                        { value: 'FEMALE', label: 'Féminin' },
                        { value: 'OTHER', label: 'Autre' },
                        { value: 'UNSPECIFIED', label: 'Non spécifié' },
                    ]}
                />
            </FormField>
            <FormField label="Numéro de licence *">
                <Input
                    value={form.licenseNumber}
                    onChange={(e) => updateField('licenseNumber', e.target.value)}
                    required
                />
            </FormField>
            <FormField label="Type professionnel *">
                <Select
                    value={form.professionalType}
                    onChange={(e) => updateField('professionalType', e.target.value)}
                    options={[
                        { value: 'CLINICIAN', label: 'Clinicien' },
                        { value: 'NUTRITIONIST', label: 'Nutritionniste' },
                    ]}
                />
            </FormField>
            <FormField label="Spécialité">
                <Input
                    value={form.specialty}
                    onChange={(e) => updateField('specialty', e.target.value)}
                />
            </FormField>
            <FormField label="URL de signature">
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
                        <h3>Vérifiez les informations</h3>
                        <p><strong>Nom complet :</strong> {form.fullName}</p>
                        <p><strong>Téléphone :</strong> {form.phone || '—'}</p>
                        <p><strong>Genre :</strong> {form.gender}</p>
                        <p><strong>Licence :</strong> {form.licenseNumber}</p>
                        <p><strong>Type :</strong> {form.professionalType}</p>
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
                        <Button variant="primary" onClick={handleSubmit} disabled={isSubmitting}>
                            {isSubmitting ? 'Enregistrement...' : 'Enregistrer'}
                        </Button>
                    )}
                </div>
            </div>
        </Modal>
    );
}
