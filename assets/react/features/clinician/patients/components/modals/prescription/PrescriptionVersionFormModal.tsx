import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createPrescriptionVersion } from '../../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientPrescription } from '../../../types';

interface PrescriptionVersionFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    prescription: PatientPrescription | null;
    currentVersionCount: number;
    onSuccess: () => void;
}

export function PrescriptionVersionFormModal({
    isOpen,
    onClose,
    prescription,
    currentVersionCount,
    onSuccess,
}: PrescriptionVersionFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({ changesSummary: '' });

    useEffect(() => {
        if (isOpen) {
            setForm({ changesSummary: '' });
            setError(null);
        }
    }, [isOpen]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!prescription) return;
        const modifiedById = getCurrentUserIdFromToken();
        if (!modifiedById) {
            setError('Impossible d\'identifier l\'auteur.');
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            await createPrescriptionVersion({
                prescriptionId: prescription.id,
                versionNumber: currentVersionCount + 1,
                changesSummary: form.changesSummary || undefined,
                data: {
                    status: prescription.status,
                    startDate: prescription.startDate,
                    endDate: prescription.endDate,
                    notes: prescription.notes,
                },
                modifiedById,
                modifiedAt: new Date().toISOString(),
            });
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la création de la version.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Archiver une version">
            {error && <Alert variant="error">{error}</Alert>}
            <p className="dossier-form__hint">
                Crée un instantané de la prescription actuelle (version {currentVersionCount + 1}).
            </p>
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Résumé des modifications" htmlFor="changesSummary">
                        <Textarea
                            id="changesSummary"
                            name="changesSummary"
                            rows={4}
                            value={form.changesSummary}
                            onChange={handleChange}
                            fullWidth
                            placeholder="ex: Modification de la posologie de l'insuline..."
                        />
                    </FormField>
                    <FormField label="Version">
                        <Input value={`v${currentVersionCount + 1}`} disabled />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Archiver'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
