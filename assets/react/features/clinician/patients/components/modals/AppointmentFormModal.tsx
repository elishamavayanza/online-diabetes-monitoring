import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientDossierData } from '../../types';
import {useAppointmentForm} from "@/react/features/clinician/patients/hooks/appointment/useAppointmentForm";

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

export function AppointmentFormModal({
                                         isOpen,
                                         onClose,
                                         data,
                                         defaultDate,
                                         defaultReason = '',
                                         onSuccess,
                                     }: AppointmentFormModalProps) {
    const { form, isLoading, handleChange, handleSubmit } = useAppointmentForm({
        data,
        defaultDate,
        defaultReason,
        onSuccess,
        onClose,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Nouveau rendez-vous">
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label="Patient">
                        <Input value={data.profile.fullName} disabled />
                    </FormField>
                    <FormField label="Date et heure" htmlFor="scheduledAt" required>
                        <Input
                            id="scheduledAt"
                            name="scheduledAt"
                            type="datetime-local"
                            value={form.scheduledAt}
                            onChange={handleChange}
                            required
                        />
                    </FormField>
                    <FormField label="Durée (min)" htmlFor="durationMinutes" required>
                        <Input
                            id="durationMinutes"
                            name="durationMinutes"
                            type="number"
                            min="5"
                            step="5"
                            value={form.durationMinutes}
                            onChange={handleChange}
                            required
                        />
                    </FormField>
                    <FormField label="Statut" htmlFor="status" required>
                        <Select
                            id="status"
                            name="status"
                            value={form.status}
                            onChange={handleChange}
                            options={STATUS_OPTIONS}
                        />
                    </FormField>
                    <FormField label="Motif" htmlFor="reason">
                        <Input
                            id="reason"
                            name="reason"
                            value={form.reason}
                            onChange={handleChange}
                            placeholder="Consultation, suivi..."
                        />
                    </FormField>
                    <FormField label="Notes" htmlFor="notes">
                        <Input id="notes" name="notes" value={form.notes} onChange={handleChange} />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        Annuler
                    </Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : 'Créer'}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
