import React from 'react';
import { Stepper } from '@/react/components/Navigation/Stepper';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { Checkbox } from '@/react/components/Forms/Checkbox';
import { Spinner } from '@/react/components/UI/Spinner';
import { Card } from '@/react/components/UI/Card';
import { ProfessionalFormFields } from "@/react/features/root/users/components/ProfessionalFormFields";
import { AddressFields } from "@/react/features/root/users/components/AddressFields";
import { AvatarUpload } from "@/react/features/root/users/components/AvatarUpload";
import { useProfessionalCreatePage } from '../hooks/useProfessionalCreatePage';
import '@/styles/pages/admin/professionals/_professional-create.scss';

export function ProfessionalCreatePage() {
    const {
        form, updateField, updateAddress, updateAvatar, submit: handleSubmit, isSubmitting, error,
        step, patients, selectedPatients, isLoadingPatients, patientsError, steps, activeStepId,
        handleStepChange, handleNext, handlePrev, togglePatient, toggleAllPatients,
    } = useProfessionalCreatePage();

    const renderStepContent = () => {
        switch (step) {
            case 0:
                return (
                    <div className="step-infos-address">
                        <Card header={<h3>Informations</h3>}>
                            <ProfessionalFormFields form={form} updateField={updateField} />
                        </Card>
                        <Card header={<h3>Adresse</h3>}>
                            <AddressFields address={form.address} onChange={updateAddress} />
                        </Card>
                    </div>
                );
            case 1:
                return (
                    <div className="step-photo">
                        <AvatarUpload
                            value={form.avatarUrl}
                            name={form.fullName}
                            onChange={updateAvatar}
                        />
                    </div>
                );
            case 2:
                return (
                    <div className="step-attach-patients">
                        {isLoadingPatients ? (
                            <div className="step-attach-patients__loading">
                                <Spinner />
                                <p>Chargement des patients...</p>
                            </div>
                        ) : patientsError ? (
                            <Alert variant="error">{patientsError}</Alert>
                        ) : (
                            <Card
                                className="attach-patients-card"
                                header={
                                    <div className="attach-patients-card__header">
                                        <h3>Patients de l'organisation</h3>
                                        <span className="attach-patients-card__count">
                                          {selectedPatients.length} sélectionné(s)
                                        </span>
                                    </div>
                                }
                                footer={
                                    <div className="attach-patients-card__footer">
                                        <Checkbox
                                            label="Tout sélectionner"
                                            checked={selectedPatients.length === patients.length}
                                            onChange={toggleAllPatients}
                                        />
                                    </div>
                                }
                            >
                                <div className="attach-patients-card__list">
                                    {patients.map((patient) => (
                                        <div
                                            key={patient.id}
                                            className={`attach-patient-item ${
                                                selectedPatients.includes(patient.id) ? 'selected' : ''
                                            }`}
                                            onClick={() => togglePatient(patient.id)}
                                        >
                                            <Checkbox
                                                label={patient.nom}
                                                checked={selectedPatients.includes(patient.id)}
                                                onChange={() => togglePatient(patient.id)}
                                            />
                                        </div>
                                    ))}
                                </div>
                            </Card>
                        )}
                    </div>
                );
            case 3:
                return (
                    <div className="step-summary">
                        <div className="step-summary__header">
                            <h3>Vérifiez les informations</h3>
                        </div>
                        <div className="step-summary__content">
                            <div className="step-summary__avatar">
                                {form.avatarUrl ? (
                                    <img src={form.avatarUrl} alt="Avatar" className="avatar-image" />
                                ) : (
                                    <div className="avatar-placeholder">
                                        {form.fullName?.charAt(0)?.toUpperCase() || '?'}
                                    </div>
                                )}
                            </div>
                            <div className="step-summary__details">
                                <p><strong>Nom complet :</strong> {form.fullName}</p>
                                <p><strong>Email :</strong> {form.email}</p>
                                <p><strong>Téléphone :</strong> {form.phone || '—'}</p>
                                <p><strong>Genre :</strong> {form.gender}</p>
                                <p><strong>Licence :</strong> {form.licenseNumber || '—'}</p>
                                <p><strong>Spécialité :</strong> {form.specialty || '—'}</p>
                                <p><strong>Adresse :</strong> {form.address.street} {form.address.city} {form.address.postalCode} {form.address.country}</p>
                                <p><strong>Patients attachés :</strong> {selectedPatients.length}</p>
                            </div>
                        </div>
                    </div>
                );
            default:
                return null;
        }
    };

    return (
        <div className="professional-create-page">
            <div className="professional-create-page__header">
                <h1>Ajouter un professionnel</h1>
                <p>Complétez les informations, la photo et attachez des patients</p>
            </div>
            {error && <Alert variant="error">{error}</Alert>}

            <div className="professional-create-page__stepper">
                <Stepper
                    steps={steps}
                    activeStepId={activeStepId}
                    onStepChange={handleStepChange}
                />
            </div>

            <div className="professional-create-page__content">
                {renderStepContent()}
            </div>

            <div className="professional-create-page__footer">
                {step > 0 && (
                    <Button variant="outline" onClick={handlePrev}>
                        Précédent
                    </Button>
                )}
                {step < steps.length - 1 ? (
                    <Button variant="primary" onClick={handleNext}>
                        Suivant
                    </Button>
                ) : (
                    <Button variant="primary" onClick={handleSubmit} disabled={isSubmitting}>
                        {isSubmitting ? 'Création...' : 'Créer'}
                    </Button>
                )}
            </div>
        </div>
    );
}
