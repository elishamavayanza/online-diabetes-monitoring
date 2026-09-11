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
import { Select } from "@/react/components/Forms/Select";
import { useI18n } from '@/react/i18n/I18nContext';

interface AttachPatientModalProps {
    isOpen: boolean;
    onClose: () => void;
    professionalId: string;
    onSuccess?: () => void; // ✅ pour rafraîchir après affectation
}

export function AttachPatientModal({ isOpen, onClose, professionalId, onSuccess }: AttachPatientModalProps) {
    const { patients, form, updateField, submit, isSubmitting, error } = useAttachPatient(professionalId);
    const { t } = useI18n();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const success = await submit();
        if (success) {
            onSuccess?.();
            onClose();
        }
    };

    const patientOptions = patients.map((p) => ({ value: p.id, label: p.nom }));

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="medium">
            <div className="attach-patient-modal">
                <h2>{t('Attacher un patient')}</h2>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit} className="attach-patient-modal__form">
                    <div className="attach-patient-modal__grid">
                        <FormField label={t('Patient *')}>
                            <SearchableSelect
                                value={form.patientId}
                                onChange={(value) => updateField('patientId', Number(value))} //  conversion en number
                                options={patientOptions}
                                placeholder={t('Rechercher un patient...')}
                                required
                            />
                        </FormField>

                        <FormField label={t('Rôle *')}>
                            <Select
                                value={form.role}
                                onChange={(e) => updateField('role', e.target.value as any)}
                                options={[
                                    { value: 'PRIMARY_CLINICIAN', label: t('Médecin principal') },
                                    { value: 'SPECIALIST', label: t('Spécialiste') },
                                    { value: 'NUTRITIONIST', label: t('Nutritionniste') },
                                ]}
                            />
                        </FormField>

                        <FormField label={t('Date de début *')}>
                            <Input
                                type="date"
                                value={form.startDate}
                                onChange={(e) => updateField('startDate', e.target.value)}
                                required
                            />
                        </FormField>

                        <FormField label={t('Date de fin')}>
                            <Input
                                type="date"
                                value={form.endDate ?? ''}
                                onChange={(e) => updateField('endDate', e.target.value || null)}
                            />
                        </FormField>

                        <FormField label={t('Actif')}>
                            <Switch
                                checked={form.active}
                                onChange={(e) => updateField('active', e.target.checked)}
                            />
                        </FormField>
                    </div>

                    <div className="attach-patient-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>{t('Annuler')}</Button>
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? t('Affectation...') : t('Attacher')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}
