import { useState } from 'react';
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

export function IntakeActionModal({ isOpen, onClose, intake, onConfirm, isSubmitting }: IntakeActionModalProps) {
    const [status, setStatus] = useState<IntakeStatus>('TAKEN');
    const [time, setTime] = useState('');
    const [quantity, setQuantity] = useState('1');

    if (!intake) return null;

    const handleConfirm = async () => {
        await onConfirm(status, time || intake.time, quantity);
        setStatus('TAKEN');
        setTime('');
        setQuantity('1');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Enregistrer la prise">
            <div className="intake-action-modal">
                {/* Affichage du médicament */}
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
                    <Input
                        type="time"
                        value={time}
                        onChange={(e) => setTime(e.target.value)}
                        placeholder={intake.time}
                    />
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
                    <Button variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button variant="primary" onClick={handleConfirm} disabled={isSubmitting}>
                        {isSubmitting ? 'Enregistrement...' : 'Valider'}
                    </Button>
                </div>
            </div>
        </Modal>
    );
}
