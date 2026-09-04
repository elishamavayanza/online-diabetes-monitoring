// components/CancelAppointmentModal.tsx
import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { PatientAppointment } from '../types';

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

    if (!appointment) return null;

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        await onConfirm(appointment.id, reason.trim() || 'Annulation');
        setReason('');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Annuler le rendez-vous">
            <form onSubmit={handleSubmit} className="dossier-form">
                <p>
                    Voulez-vous annuler le rendez-vous du {appointment.date} à {appointment.heure}
                    {' '}avec {appointment.professionnel} ?
                </p>
                <FormField label="Motif d'annulation">
                    <Textarea
                        value={reason}
                        onChange={(e) => setReason(e.target.value)}
                        placeholder="Expliquez pourquoi vous annulez..."
                        rows={4}
                    />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>
                        Retour
                    </Button>
                    <Button type="submit" variant="danger" disabled={isSubmitting}>
                        {isSubmitting ? 'Annulation...' : 'Confirmer l’annulation'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
