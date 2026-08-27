import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Stepper } from '@/react/components/Navigation/Stepper';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useUserForm } from '../hooks/useUserForm';
import { ProfessionalFormFields } from './ProfessionalFormFields';
import { PatientFormFields } from './PatientFormFields';
import { AddressFields } from './AddressFields';
import { AvatarUpload } from './AvatarUpload';

interface UserFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    mode?: 'create' | 'edit';
    initialUser?: { type: 'patient' | 'professional'; data: any };
}

export function UserFormModal({ isOpen, onClose, mode = 'create', initialUser }: UserFormModalProps) {
    const {
        userType,
        setUserType,
        step,
        setStep,
        isProfessional,
        isPatient,
        createProfessional,
        updateProfessional,
        createPatient,
        updatePatient,
        handleSubmit,
    } = useUserForm(mode, initialUser);

    const steps = [
        { id: 'type', label: 'Type de compte', disabled: mode === 'edit' }, // ❌ désactivée en édition
        { id: 'infos', label: 'Informations' },
        { id: 'address', label: 'Adresse' },
        { id: 'photo', label: 'Photo' },
        { id: 'summary', label: 'Récapitulatif' },
    ];

    const currentForm = isProfessional
        ? mode === 'create' ? createProfessional : updateProfessional
        : mode === 'create' ? createPatient : updatePatient;

    const isSubmitting = currentForm.isSubmitting;
    const error = currentForm.error;
    const address = currentForm.form.address;

    const updateAddress = (field: keyof typeof address, value: string) => {
        if (isProfessional) {
            (currentForm as typeof createProfessional).updateAddress(field as any, value);
        } else {
            (currentForm as typeof createPatient).updateAddress(field as any, value);
        }
    };

    const updateAvatar = (url: string, file: File | null) => {
        if (isProfessional) {
            (currentForm as typeof createProfessional).updateAvatar(url, file);
        } else {
            (currentForm as typeof createPatient).updateAvatar(url, file);
        }
    };

    const activeStepId = steps[step].id;

    const handleStepChange = (newStepId: string) => {
        const index = steps.findIndex((s) => s.id === newStepId);
        if (index >= 0) setStep(index);
    };

    const renderStepContent = () => {
        if (step === 0) {
            return (
                <div>
                    <p>Choisissez le type de compte à créer :</p>
                    <div style={{ display: 'flex', gap: '1rem' }}>
                        <Button
                            variant={userType === 'patient' ? 'primary' : 'outline'}
                            onClick={() => {
                                setUserType('patient');
                                setStep(1);
                            }}
                        >
                            Patient
                        </Button>
                        <Button
                            variant={userType === 'professional' ? 'primary' : 'outline'}
                            onClick={() => {
                                setUserType('professional');
                                setStep(1);
                            }}
                        >
                            Professionnel
                        </Button>
                    </div>
                </div>
            );
        }
        if (step === 1) {
            return isProfessional ? (
                <ProfessionalFormFields
                    form={(currentForm as typeof createProfessional).form}
                    updateField={(currentForm as typeof createProfessional).updateField}
                />
            ) : (
                <PatientFormFields
                    form={(currentForm as typeof createPatient).form}
                    updateField={(currentForm as typeof createPatient).updateField}
                />
            );
        }
        if (step === 2) {
            return <AddressFields address={address} onChange={updateAddress} />;
        }
        if (step === 3) {
            return (
                <AvatarUpload
                    value={currentForm.form.avatarUrl}
                    name={currentForm.form.fullName}
                    onChange={updateAvatar}
                />
            );
        }
        if (step === 4) {
            const form = currentForm.form;
            return (
                <div className="user-form-modal__summary">
                    <h3>Vérifiez les informations</h3>
                    <p><strong>Type :</strong> {isProfessional ? 'Professionnel' : 'Patient'}</p>
                    <p><strong>Nom complet :</strong> {form.fullName}</p>
                    <p><strong>Email :</strong> {form.email}</p>
                    <p><strong>Téléphone :</strong> {form.phone || '—'}</p>
                    <p><strong>Genre :</strong> {form.gender}</p>
                    <p><strong>Adresse :</strong> {address.street} {address.city} {address.postalCode} {address.country}</p>
                    {isProfessional && (
                        <>
                            <p><strong>Licence :</strong> {(form as any).licenseNumber}</p>
                            <p><strong>Spécialité :</strong> {(form as any).specialty || '—'}</p>
                        </>
                    )}
                    {isPatient && (
                        <>
                            <p><strong>Date de naissance :</strong> {(form as any).dateOfBirth || '—'}</p>
                            <p><strong>Groupe sanguin :</strong> {(form as any).bloodType || '—'}</p>
                        </>
                    )}
                </div>
            );
        }
        return null;
    };

    const handleNext = () => {
        setStep((prev) => Math.min(prev + 1, steps.length - 1));
    };
    const handlePrev = () => {
        setStep((prev) => Math.max(prev - 1, 0));
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="large">
            <div className="user-form-modal">
                <h2>{mode === 'create' ? 'Créer un utilisateur' : 'Modifier l’utilisateur'}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Stepper
                    steps={steps}
                    activeStepId={activeStepId}
                    onStepChange={handleStepChange}
                />
                <div className="user-form-modal__content">{renderStepContent()}</div>
                <div className="user-form-modal__footer">
                    {step > 0 && (
                        <Button variant="outline" onClick={handlePrev}>
                            Précédent
                        </Button>
                    )}
                    {step === 0 ? null : step < steps.length - 1 ? (
                        <Button variant="primary" onClick={handleNext}>
                            Suivant
                        </Button>
                    ) : (
                        <Button variant="primary" onClick={handleSubmit} disabled={isSubmitting}>
                            {mode === 'create' ? 'Créer' : 'Enregistrer'}
                        </Button>
                    )}
                </div>
            </div>
        </Modal>
    );
}
