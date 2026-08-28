import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Switch } from '@/react/components/Forms/Switch';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useAttachPatient } from '../hooks/useAttachPatient';
import { SearchableSelect } from '@/react/components/Forms/SearchableSelect/SearchableSelect';
import {Select} from "@/react/components/Forms/Select";

interface AttachPatientModalProps {
    isOpen: boolean;
    onClose: () => void;
    professionalId: string;
}

export function AttachPatientModal({ isOpen, onClose, professionalId }: AttachPatientModalProps) {
    const { patients, form, updateField, submit, isSubmitting, error } = useAttachPatient(professionalId);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        submit();
    };

    const patientOptions = patients.map((p) => ({ value: p.id, label: p.nom }));

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="attach-patient-modal">
                <h2>Attacher un patient</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit} className="attach-patient-modal__form">
                    <div className="attach-patient-modal__grid">
                        <FormField label="Patient *">
                            <SearchableSelect
                                value={form.patientId}
                                onChange={(value) => updateField('patientId', value)}
                                options={patientOptions}
                                placeholder="Rechercher un patient..."
                                required
                            />
                        </FormField>

                        <FormField label="Rôle *">
                            <Select
                                value={form.role}
                                onChange={(e) => updateField('role', e.target.value as any)}
                                options={[
                                    { value: 'PRIMARY_CLINICIAN', label: 'Médecin principal' },
                                    { value: 'SPECIALIST', label: 'Spécialiste' },
                                    { value: 'NUTRITIONIST', label: 'Nutritionniste' },
                                ]}
                            />
                        </FormField>

                        <FormField label="Date de début *">
                            <Input
                                type="date"
                                value={form.startDate}
                                onChange={(e) => updateField('startDate', e.target.value)}
                                required
                            />
                        </FormField>

                        <FormField label="Date de fin">
                            <Input
                                type="date"
                                value={form.endDate}
                                onChange={(e) => updateField('endDate', e.target.value)}
                            />
                        </FormField>

                        <FormField label="Actif">
                            <Switch
                                checked={form.active}
                                onChange={(e) => updateField('active', e.target.checked)}
                            />
                        </FormField>
                    </div>

                    <div className="attach-patient-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Affectation...' : 'Attacher'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
