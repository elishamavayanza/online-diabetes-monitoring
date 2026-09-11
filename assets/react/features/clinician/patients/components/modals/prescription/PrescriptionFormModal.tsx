import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientDossierData } from '../../../types';
import {usePrescriptionForm} from "@/react/features/clinician/patients/hooks/prescription/usePrescriptionForm";
import { useI18n } from '@/react/i18n/I18nContext';

interface PrescriptionFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    onSuccess: () => void;
}

const STATUS_OPTIONS = [
    { value: 'ACTIVE', label: 'Active' },
    { value: 'DRAFT', label: 'Brouillon' },
];

export function PrescriptionFormModal({
                                          isOpen,
                                          onClose,
                                          data,
                                          onSuccess,
                                      }: PrescriptionFormModalProps) {
    const { t } = useI18n();
    const {
        form,
        handleChange,
        handleSubmit,
        isLoading,
        error,
    } = usePrescriptionForm({ isOpen, onClose, data, onSuccess });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Nouvelle prescription')}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label={t('Patient')}>
                        <Input value={data.profile.fullName} disabled />
                    </FormField>
                    <FormField label={t('Date de début')} htmlFor="startDate" required>
                        <Input
                            id="startDate"
                            name="startDate"
                            type="datetime-local"
                            value={form.startDate}
                            onChange={handleChange}
                            required
                        />
                    </FormField>
                    <FormField label={t('Date de fin')} htmlFor="endDate">
                        <Input
                            id="endDate"
                            name="endDate"
                            type="datetime-local"
                            value={form.endDate}
                            onChange={handleChange}
                        />
                    </FormField>
                    <FormField label={t('Statut')} htmlFor="status" required>
                        <Select
                            id="status"
                            name="status"
                            value={form.status}
                            onChange={handleChange}
                            options={STATUS_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))}
                        />
                    </FormField>
                    <FormField label={t('Notes / Ordonnance')} htmlFor="notes">
                        <Textarea
                            id="notes"
                            name="notes"
                            rows={4}
                            value={form.notes}
                            onChange={handleChange}
                            fullWidth
                        />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        {t('Annuler')}
                    </Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : t('Créer')}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
