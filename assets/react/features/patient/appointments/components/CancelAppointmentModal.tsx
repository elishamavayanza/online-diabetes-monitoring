// components/CancelAppointmentModal.tsx
import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { PatientAppointment } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface CancelAppointmentModalProps {
    isOpen: boolean;
    onClose: () => void;
    appointment: PatientAppointment | null;
    onConfirm: (appointmentId: string, reason: string) => Promise<void>;
    isSubmitting: boolean;
}

export function CancelAppointmentModal({
                                           isOpen,
                                           onClose,
                                           appointment,
                                           onConfirm,
                                           isSubmitting,
                                       }: CancelAppointmentModalProps) {
const [reason, setReason] = useState('');
    const { t } = useI18n();

    if (!appointment) return null;

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await onConfirm(appointment.id, reason.trim() || t('Annulation'));
        setReason('');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Annuler le rendez-vous')}>
            <form onSubmit={handleSubmit} className="dossier-form">
                <p>
                    {t('Voulez-vous annuler le rendez-vous du {{ date }} à {{ heure }} avec {{ professionnel }} ?', {
                        date: appointment.date,
                        heure: appointment.heure,
                        professionnel: appointment.professionnel,
                    })}
                </p>
                <FormField label={t("Motif d'annulation")}>
                    <Textarea
                        value={reason}
                        onChange={(e) => setReason(e.target.value)}
                        placeholder={t('Expliquez pourquoi vous annulez...')}
                        rows={4}
                    />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>
                        {t('Retour')}
                    </Button>
                    <Button type="submit" variant="danger" disabled={isSubmitting}>
                        {isSubmitting ? t('Annulation...') : t('Confirmer l\u2019annulation')}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
