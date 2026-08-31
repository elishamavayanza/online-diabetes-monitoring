import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createMedicalNote } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';

interface MedicalNoteFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    defaultDate?: Date | null;
    onSuccess: () => void;
}

function toDatetimeLocalValue(date?: Date | null): string {
    if (!date) {
        const now = new Date();
        const pad = (n: number) => String(n).padStart(2, '0');
        return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    }
    const d = new Date(date);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

export function MedicalNoteFormModal({ isOpen, onClose, data, defaultDate, onSuccess }: MedicalNoteFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({ content: '', notedAt: '' });

    useEffect(() => {
        if (isOpen) {
            setForm({ content: '', notedAt: toDatetimeLocalValue(defaultDate) });
            setError(null);
        }
    }, [isOpen, defaultDate]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const authorId = getCurrentUserIdFromToken();
        const medicalRecordId = data.record?.id;
        if (!authorId || !medicalRecordId) {
            setError('Dossier médical ou auteur introuvable.');
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            await createMedicalNote({
                medicalRecordId,
                authorId,
                content: form.content,
                notedAt: new Date(form.notedAt).toISOString(),
            });
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la création.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouvelle note médicale">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <FormField label="Date de la note" htmlFor="notedAt" required>
                    <Input id="notedAt" name="notedAt" type="datetime-local" value={form.notedAt}
                        onChange={(e) => setForm((p) => ({ ...p, notedAt: e.target.value }))} required />
                </FormField>
                <FormField label="Contenu" htmlFor="content" required>
                    <Textarea id="content" name="content" rows={6} value={form.content} fullWidth required
                        onChange={(e) => setForm((p) => ({ ...p, content: e.target.value }))} />
                </FormField>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
