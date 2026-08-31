import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { createAppointment } from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '../../types';

interface AppointmentFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    defaultDate?: Date | null;
    defaultReason?: string;
    onSuccess: () => void;
}

const STATUS_OPTIONS = [
    { value: 'SCHEDULED', label: 'Planifié' },
    { value: 'CONFIRMED', label: 'Confirmé' },
    { value: 'COMPLETED', label: 'Terminé' },
];

function toDatetimeLocalValue(date?: Date | null): string {
    if (!date) return '';
    const d = new Date(date);
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

export function AppointmentFormModal({
    isOpen,
    onClose,
    data,
    defaultDate,
    defaultReason = '',
    onSuccess,
}: AppointmentFormModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState({
        scheduledAt: '',
        durationMinutes: '30',
        status: 'SCHEDULED',
        reason: defaultReason,
        notes: '',
    });

    useEffect(() => {
        if (isOpen) {
            setForm({
                scheduledAt: defaultDate ? toDatetimeLocalValue(defaultDate) : '',
                durationMinutes: '30',
                status: defaultReason ? 'CONFIRMED' : 'SCHEDULED',
                reason: defaultReason,
                notes: '',
            });
            setError(null);
        }
    }, [isOpen, defaultDate, defaultReason]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const professionalId = getCurrentUserIdFromToken();
        const organizationId = data.profile.organizationId;
        if (!professionalId || !organizationId) {
            setError('Impossible d\'identifier le professionnel ou l\'organisation.');
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            await createAppointment({
                patientId: data.profile.id,
                professionalId,
                organizationId,
                scheduledAt: new Date(form.scheduledAt).toISOString(),
                durationMinutes: Number(form.durationMinutes),
                status: form.status,
                reason: form.reason || undefined,
                notes: form.notes || undefined,
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
        <Modal isOpen={isOpen} onClose={onClose} title="Nouveau rendez-vous">
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Patient">
                        <Input value={data.profile.fullName} disabled />
                    </FormField>
                    <FormField label="Date et heure" htmlFor="scheduledAt" required>
                        <Input id="scheduledAt" name="scheduledAt" type="datetime-local" value={form.scheduledAt} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Durée (min)" htmlFor="durationMinutes" required>
                        <Input id="durationMinutes" name="durationMinutes" type="number" min="5" step="5" value={form.durationMinutes} onChange={handleChange} required />
                    </FormField>
                    <FormField label="Statut" htmlFor="status" required>
                        <Select id="status" name="status" value={form.status} onChange={handleChange} options={STATUS_OPTIONS} />
                    </FormField>
                    <FormField label="Motif" htmlFor="reason">
                        <Input id="reason" name="reason" value={form.reason} onChange={handleChange} placeholder="Consultation, suivi..." />
                    </FormField>
                    <FormField label="Notes" htmlFor="notes">
                        <Input id="notes" name="notes" value={form.notes} onChange={handleChange} />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>Annuler</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Créer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
