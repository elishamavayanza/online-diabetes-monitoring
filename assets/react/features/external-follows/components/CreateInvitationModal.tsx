import React, { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { SearchableSelect } from '@/react/components/Forms/SearchableSelect/SearchableSelect';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateExternalFollow } from '../hooks/useCreateExternalFollow';
import { fetchPatients } from '@/react/features/admin/patients/services/patientsService';

interface CreateInvitationModalProps {
    isOpen: boolean;
    organizationId: string;
    onClose: () => void;
    onSuccess?: () => void;
}

const DURATION_PRESETS = [
    { value: 30, label: '30 jours' },
    { value: 90, label: '90 jours' },
    { value: 180, label: '180 jours' },
    { value: 365, label: '365 jours' },
];

export function CreateInvitationModal({ isOpen, organizationId, onClose, onSuccess }: CreateInvitationModalProps) {
    const { form, updateField, submit, isSubmitting, error, reset } = useCreateExternalFollow(organizationId, { onSuccess });
    const [patientOptions, setPatientOptions] = useState<{ value: string; label: string }[]>([]);
    const [presetMode, setPresetMode] = useState<string>('90');

    useEffect(() => {
        if (!isOpen) return;
        reset();
        setPresetMode('90');
        let cancelled = false;
        fetchPatients({ search: '', typeDiabete: 'Tous' })
            .then((patients) => {
                if (!cancelled) {
                    setPatientOptions(patients.map((p) => ({ value: p.id, label: p.nom })));
                }
            })
            .catch(() => {
                if (!cancelled) setPatientOptions([]);
            });
        return () => {
            cancelled = true;
        };
    }, [isOpen, organizationId]);

    const handlePresetChange = (value: string) => {
        setPresetMode(value);
        if (value !== 'custom') {
            updateField('durationDays', Number(value));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Inviter un professionnel externe" size="large">
            <div className="external-follow-form">
                {error && <Alert variant="error">{error}</Alert>}
                <p className="external-follow-form__intro">
                    Invitez un professionnel d'une autre organisation à suivre un patient pour une durée définie.
                    Le professionnel doit déjà disposer d'un compte.
                </p>
                <Form onSubmit={handleSubmit}>
                    <FormField label="Patient à suivre *">
                        <SearchableSelect
                            placeholder="Rechercher un patient..."
                            options={patientOptions}
                            value={form.patientId}
                            onChange={(value) => updateField('patientId', value)}
                        />
                    </FormField>
                    <FormField label="Email du professionnel invité *">
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                            placeholder="dr.dupont@centre2.com"
                            required
                        />
                    </FormField>
                    <FormField label="Durée de l'accès *">
                        <Select
                            value={presetMode}
                            onChange={(e) => handlePresetChange(e.target.value)}
                            options={[...DURATION_PRESETS, { value: 'custom', label: 'Durée personnalisée...' }]}
                        />
                    </FormField>
                    {presetMode === 'custom' && (
                        <FormField label="Nombre de jours *">
                            <Input
                                type="number"
                                min={1}
                                max={730}
                                value={String(form.durationDays)}
                                onChange={(e) => updateField('durationDays', Number(e.target.value))}
                                required
                            />
                        </FormField>
                    )}
                    <FormField label="Message (optionnel)">
                        <Textarea
                            value={form.message}
                            onChange={(e) => updateField('message', e.target.value)}
                            placeholder="Ex: Merci de suivre ce patient pendant sa grossesse..."
                        />
                    </FormField>
                    <div className="external-follow-form__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Envoi...' : 'Envoyer l' + '\u2019' + 'invitation'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}