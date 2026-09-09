// components/RequestAppointmentModal.tsx
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { Textarea } from '@/react/components/Forms/Textarea';
import { useRequestAppointmentModal } from '../hooks/useRequestAppointmentModal';
import { useI18n } from '@/react/i18n/I18nContext';

interface RequestAppointmentModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

export function RequestAppointmentModal({
                                            isOpen,
                                            onClose,
                                            onSuccess,
                                        }: RequestAppointmentModalProps) {
    const {
        professionals,
        professionalId,
        setProfessionalId,
        date,
        setDate,
        time,
        setTime,
        duration,
        setDuration,
        reason,
        setReason,
        notes,
        setNotes,
        isSubmitting,
        handleSubmit,
    } = useRequestAppointmentModal({ isOpen, onClose, onSuccess });
    const { t } = useI18n();

    const professionalOptions = professionals.map((p) => ({
        value: p.id,
        label: p.fullName,
    }));

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Demander un rendez-vous')}>
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label={t('Professionnel *')}>
                    <Select
                        value={professionalId}
                        onChange={(e) => setProfessionalId(e.target.value)}
                        options={professionalOptions}
                        required
                    />
                </FormField>
                <FormField label={t('Date *')}>
                    <Input type="date" value={date} onChange={(e) => setDate(e.target.value)} required />
                </FormField>
                <FormField label={t('Heure *')}>
                    <Input type="time" value={time} onChange={(e) => setTime(e.target.value)} required />
                </FormField>
                <FormField label={t('Durée (minutes)')}>
                    <Input
                        type="number"
                        min="10"
                        step="5"
                        value={duration}
                        onChange={(e) => setDuration(e.target.value)}
                    />
                </FormField>
                <FormField label={t('Motif')}>
                    <Input
                        value={reason}
                        onChange={(e) => setReason(e.target.value)}
                        placeholder={t('Consultation, suivi...')}
                    />
                </FormField>
                <FormField label={t('Notes')}>
                    <Textarea
                        value={notes}
                        onChange={(e) => setNotes(e.target.value)}
                        placeholder={t('Informations complémentaires...')}
                    />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>
                        {t('Annuler')}
                    </Button>
                    <Button type="submit" variant="primary" disabled={isSubmitting}>
                        {isSubmitting ? t('Envoi...') : t('Envoyer la demande')}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
