// CloseMedicalRecordModal.tsx
import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Spinner } from '@/react/components/UI/Spinner';
import { closeMedicalRecord } from '../../services/dossierActionsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

interface CloseMedicalRecordModalProps {
    isOpen: boolean;
    onClose: () => void;
    medicalRecordId: string;
    onSuccess: () => void; // pour recharger les données après fermeture
}

export function CloseMedicalRecordModal({
                                            isOpen,
                                            onClose,
                                            medicalRecordId,
                                            onSuccess,
                                        }: CloseMedicalRecordModalProps) {
    const { showToast } = useToast();
    const [motif, setMotif] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!motif.trim()) {
            showToast({ type: 'error', message: 'Le motif est requis.' });
            return;
        }

        setIsLoading(true);
        try {
            await closeMedicalRecord(medicalRecordId, motif);
            showToast({ type: 'success', message: 'Dossier fermé avec succès.' });
            onSuccess();
            onClose();
        } catch (err) {
            showToast({
                type: 'error',
                message: err instanceof Error ? err.message : 'Erreur lors de la fermeture.',
            });
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Fermer le dossier médical">
            <form onSubmit={handleSubmit}>
                <FormField label="Motif de fermeture" htmlFor="motif" required>
                    <Textarea
                        id="motif"
                        value={motif}
                        onChange={(e) => setMotif(e.target.value)}
                        placeholder="Ex. Patient transféré, fin de suivi..."
                        rows={3}
                        required
                    />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        Annuler
                    </Button>
                    <Button type="submit" variant="danger" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Fermer le dossier'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
