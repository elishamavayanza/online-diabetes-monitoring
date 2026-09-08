import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { DateRangePicker } from '@/react/components/Forms/DateRangePicker/DateRangePicker';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useRenewExternalFollow } from '../hooks/useRenewExternalFollow';

interface RenewModalProps {
    isOpen: boolean;
    organizationId: string;
    invitationId: string;
    patientName: string;
    onClose: () => void;
    onSuccess?: () => void;
}

const DURATION_PRESETS = [
    { value: 30, label: '+ 30 jours' },
    { value: 90, label: '+ 90 jours' },
    { value: 180, label: '+ 180 jours' },
    { value: 365, label: '+ 365 jours' },
];

export function RenewModal({ isOpen, organizationId, invitationId, patientName, onClose, onSuccess }: RenewModalProps) {
    const { durationDays, setDurationDays, startDate, setStartDate, endDate, setEndDate, submit, isSubmitting, error } =
        useRenewExternalFollow(organizationId, { onSuccess });
    const [presetMode, setPresetMode] = useState<string>('90');

    const handlePresetChange = (value: string) => {
        setPresetMode(value);
        if (value !== 'custom') {
            setDurationDays(Number(value));
            setStartDate('');
            setEndDate('');
        } else {
            setDurationDays(null);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit(invitationId);
        if (success) {
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Prolonger le délai" size="medium">
            <div className="external-follow-form">
                {error && <Alert variant="error">{error}</Alert>}
                <p className="external-follow-form__intro">
                    Prolongez l'accès au dossier de <strong>{patientName}</strong>. Chaque jour ajouté
                    s'applique à partir de la fin du délai courant, ou définissez une nouvelle période.
                </p>
                <Form onSubmit={handleSubmit}>
                    <FormField label="Durée ajoutée *">
                        <Select
                            value={presetMode}
                            onChange={(e) => handlePresetChange(e.target.value)}
                            options={[...DURATION_PRESETS, { value: 'custom', label: 'Période personnalisée (de / à)...' }]}
                        />
                    </FormField>
                    {presetMode === 'custom' && (
                        <FormField label="Nouvelle période (de / à) *">
                            <DateRangePicker
                                labelStart="Date de début"
                                labelEnd="Date de fin"
                                startDate={startDate}
                                endDate={endDate}
                                onChange={(range) => {
                                    setStartDate(range.startDate);
                                    setEndDate(range.endDate);
                                }}
                            />
                        </FormField>
                    )}
                    <div className="external-follow-form__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Prolongement...' : 'Prolonger'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}