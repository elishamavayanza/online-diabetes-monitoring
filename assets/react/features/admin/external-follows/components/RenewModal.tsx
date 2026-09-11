import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { DateRangePicker } from '@/react/components/Forms/DateRangePicker/DateRangePicker';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useRenewExternalFollow } from '../hooks/useRenewExternalFollow';
import { useI18n } from '@/react/i18n/I18nContext';

interface RenewModalProps {
    isOpen: boolean;
    organizationId: string;
    invitationId: string;
    patientName: string;
    onClose: () => void;
    onSuccess?: () => void;
}

const DURATION_PRESETS = [
    { value: 30, labelKey: '+ 30 jours' },
    { value: 90, labelKey: '+ 90 jours' },
    { value: 180, labelKey: '+ 180 jours' },
    { value: 365, labelKey: '+ 365 jours' },
];

export function RenewModal({ isOpen, organizationId, invitationId, patientName, onClose, onSuccess }: RenewModalProps) {
    const { durationDays, setDurationDays, startDate, setStartDate, endDate, setEndDate, submit, isSubmitting, error } =
        useRenewExternalFollow(organizationId, { onSuccess });
    const [presetMode, setPresetMode] = useState<string>('90');
    const { t } = useI18n();

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
        <Modal isOpen={isOpen} onClose={onClose} title={t('Prolonger le délai')} size="medium">
            <div className="external-follow-form">
                {error && <Alert variant="error">{error}</Alert>}
                <p className="external-follow-form__intro">
                    {t("Prolongez l'accès au dossier de {{ name }}. Chaque jour ajouté s'applique à partir de la fin du délai courant, ou définissez une nouvelle période.", { name: patientName })}
                </p>
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Durée ajoutée *')}>
                        <Select
                            value={presetMode}
                            onChange={(e) => handlePresetChange(e.target.value)}
                            options={[...DURATION_PRESETS.map(p => ({ value: p.value, label: t(p.labelKey) })), { value: 'custom', label: t('Période personnalisée (de / à)...') }]}
                        />
                    </FormField>
                    {presetMode === 'custom' && (
                        <FormField label={t('Nouvelle période (de / à) *')}>
                            <DateRangePicker
                                labelStart={t('Date de début')}
                                labelEnd={t('Date de fin')}
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
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Prolongement...') : t('Prolonger')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}