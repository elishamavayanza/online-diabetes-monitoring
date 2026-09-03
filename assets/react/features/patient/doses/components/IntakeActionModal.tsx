import { useState, useEffect } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { MedicationIntake, IntakeStatus } from '../types';

interface IntakeActionModalProps {
    isOpen: boolean;
    onClose: () => void;
    intake: MedicationIntake | null;
    onConfirm: (status: IntakeStatus, time: string, quantity: string) => Promise<void>;
    isSubmitting: boolean;
}

const STATUS_OPTIONS = [
    { value: 'TAKEN', label: 'Prise' },
    { value: 'SKIPPED', label: 'Ignorée' },
    { value: 'DELAYED', label: 'Retardée' },
];

const HOUR_OPTIONS = Array.from({ length: 24 }, (_, i) => ({
    value: String(i).padStart(2, '0'),
    label: `${String(i).padStart(2, '0')} h`,
}));

//  Toutes les minutes de 00 à 59
const MINUTE_OPTIONS = Array.from({ length: 60 }, (_, i) => ({
    value: String(i).padStart(2, '0'),
    label: String(i).padStart(2, '0'),
}));

export function IntakeActionModal({ isOpen, onClose, intake, onConfirm, isSubmitting }: IntakeActionModalProps) {
    const [status, setStatus] = useState<IntakeStatus>('TAKEN');
    const [hour, setHour] = useState('08');
    const [minute, setMinute] = useState('00');
    const [quantity, setQuantity] = useState('1');

    useEffect(() => {
        if (intake) {
            const [h, m] = intake.time.split(':');
            setHour(h);
            setMinute(m);
            setQuantity('1');
            setStatus('TAKEN');
        }
    }, [intake, isOpen]);

    if (!intake) return null;

    const handleConfirm = async () => {
        const time = `${hour}:${minute}`;
        await onConfirm(status, time, quantity);
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Enregistrer la prise">
            <div className="intake-action-modal">
                <div className="intake-action-modal__medication">
                    <strong>Médicament :</strong> {intake.medication}
                </div>

                <FormField label="Statut *">
                    <Select
                        value={status}
                        onChange={(e) => setStatus(e.target.value as IntakeStatus)}
                        options={STATUS_OPTIONS}
                    />
                </FormField>

                <FormField label="Heure">
                    <div className="time-picker-24h">
                        <Select
                            value={hour}
                            onChange={(e) => setHour(e.target.value)}
                            options={HOUR_OPTIONS}
                        />
                        <span>:</span>
                        <Select
                            value={minute}
                            onChange={(e) => setMinute(e.target.value)}
                            options={MINUTE_OPTIONS}
                        />
                    </div>
                </FormField>

                <FormField label="Quantité">
                    <Input
                        type="number"
                        step="0.1"
                        min="0"
                        value={quantity}
                        onChange={(e) => setQuantity(e.target.value)}
                    />
                </FormField>

                <div className="intake-action-modal__actions">
                    <Button type="button" variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button type="button" variant="primary" onClick={handleConfirm} disabled={isSubmitting}>
                        {isSubmitting ? 'Enregistrement...' : 'Valider'}
                    </Button>
                </div>
            </div>
        </Modal>
    );
}
