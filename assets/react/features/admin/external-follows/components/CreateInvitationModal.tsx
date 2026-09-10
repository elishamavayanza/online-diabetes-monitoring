import React, { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { DateRangePicker } from '@/react/components/Forms/DateRangePicker/DateRangePicker';
import { SearchableSelect } from '@/react/components/Forms/SearchableSelect/SearchableSelect';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useCreateExternalFollow } from '../hooks/useCreateExternalFollow';
import { fetchPatients } from '@/react/features/admin/patients/services/patientsService';
import { useI18n } from '@/react/i18n/I18nContext';

interface CreateInvitationModalProps {
    isOpen: boolean;
    organizationId: string;
    onClose: () => void;
    onSuccess?: () => void;
}

const DURATION_PRESETS = [
    { value: 30, labelKey: '30 jours' },
    { value: 90, labelKey: '90 jours' },
    { value: 180, labelKey: '180 jours' },
    { value: 365, labelKey: '365 jours' },
];

export function CreateInvitationModal({ isOpen, organizationId, onClose, onSuccess }: CreateInvitationModalProps) {
    const { form, updateField, submit, isSubmitting, error, reset } = useCreateExternalFollow(organizationId, { onSuccess });
    const [patientOptions, setPatientOptions] = useState<{ value: string; label: string }[]>([]);
    const [presetMode, setPresetMode] = useState<string>('90');
    const { t } = useI18n();

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
            updateField('startDate', '');
            updateField('endDate', '');
        } else {
            updateField('durationDays', null);
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
        <Modal isOpen={isOpen} onClose={onClose} title={t("Inviter un professionnel externe")} size="large">
            <div className="external-follow-form">
                {error && <Alert variant="error">{error}</Alert>}
                <p className="external-follow-form__intro">
                    {t("Invitez un professionnel d\u2019une autre organisation à suivre un patient pour une durée définie. Le professionnel doit déjà disposer d\u2019un compte.")}
                </p>
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Patient à suivre *')}>
                        <SearchableSelect
                            placeholder={t('Rechercher un patient...')}
                            options={patientOptions}
                            value={form.patientId}
                            onChange={(value) => updateField('patientId', value)}
                        />
                    </FormField>
                    <FormField label={t('Email du professionnel invité *')}>
                        <Input
                            type="email"
                            value={form.email}
                            onChange={(e) => updateField('email', e.target.value)}
                            placeholder="dr.dupont@centre2.com"
                            required
                        />
                    </FormField>
                    <FormField label={t("Durée de l\u2019accès *")}>
                        <Select
                            value={presetMode}
                            onChange={(e) => handlePresetChange(e.target.value)}
                            options={[...DURATION_PRESETS.map(p => ({ value: p.value, label: t(p.labelKey) })), { value: 'custom', label: t('Période personnalisée (de / à)...') }]}
                        />
                    </FormField>
                    {presetMode === 'custom' && (
                        <FormField label={t("Période d\u2019accès (de / à) *")}>
                            <DateRangePicker
                                labelStart={t('Date de début')}
                                labelEnd={t('Date de fin')}
                                startDate={form.startDate}
                                endDate={form.endDate}
                                onChange={(range) => {
                                    updateField('startDate', range.startDate);
                                    updateField('endDate', range.endDate);
                                }}
                            />
                        </FormField>
                    )}
                    <FormField label={t('Message (optionnel)')}>
                        <Textarea
                            value={form.message}
                            onChange={(e) => updateField('message', e.target.value)}
                            placeholder={t("Ex: Merci de suivre ce patient pendant sa grossesse...")}
                        />
                    </FormField>
                    <div className="external-follow-form__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Envoi...') : t("Envoyer l\u2019invitation")}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}