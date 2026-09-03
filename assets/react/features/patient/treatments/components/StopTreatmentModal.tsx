import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Treatment } from '../types';
import {FormField} from "@/react/components/Forms/FormField";

interface StopTreatmentModalProps {
    isOpen: boolean;
    onClose: () => void;
    treatment: Treatment | null;
    onConfirm: (reason?: string) => Promise<void>;
    isSubmitting: boolean;
}

export function StopTreatmentModal({ isOpen, onClose, treatment, onConfirm, isSubmitting }: StopTreatmentModalProps) {
    const [reason, setReason] = useState('');

    if (!treatment) return null;

    const handleConfirm = async () => {
        await onConfirm(reason.trim() || undefined);
        setReason('');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Arrêter le traitement">
            <div className="stop-treatment-modal">
                <p>
                    Voulez-vous arrêter <strong>{treatment.nom}</strong> ({treatment.dosage}) ?
                </p>
                <FormField label="Motif (optionnel)">
                    <Textarea
                        value={reason}
                        onChange={(e) => setReason(e.target.value)}
                        placeholder="Expliquez pourquoi vous arrêtez ce traitement..."
                        rows={4}
                    />
                </FormField>
                <div className="stop-treatment-modal__actions">
                    <Button variant="secondary" onClick={onClose}>Annuler</Button>
                    <Button variant="danger" onClick={handleConfirm} disabled={isSubmitting}>
                        {isSubmitting ? 'Arrêt...' : 'Confirmer l’arrêt'}
                    </Button>
                </div>
            </div>
        </Modal>
    );
}
