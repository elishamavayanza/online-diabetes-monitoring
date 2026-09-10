import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientPrescription } from '../../../types';
import { usePrescriptionItemForm, medicationDosageHint } from "@/react/features/clinician/patients/hooks/prescription/usePrescriptionItemForm";
import { useI18n } from '@/react/i18n/I18nContext';

interface PrescriptionItemFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    prescription: PatientPrescription | null;
    onSuccess: () => void;
}

export function PrescriptionItemFormModal({
                                              isOpen,
                                              onClose,
                                              prescription,
                                              onSuccess,
                                          }: PrescriptionItemFormModalProps) {
    const { t } = useI18n();
    const {
        form,
        medications,
        selectedMedication,
        isLoading,
        error,
        handleChange,
        handleSubmit,
    } = usePrescriptionItemForm({ isOpen, onClose, prescription, onSuccess });

    const hint = medicationDosageHint(selectedMedication);

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={t('Ajouter un médicament')}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label={t('Médicament')} htmlFor="medicationId" required>
                        <Select
                            id="medicationId"
                            name="medicationId"
                            value={form.medicationId}
                            onChange={handleChange}
                            options={medications}
                            placeholder={t('Sélectionner un médicament')}
                        />
                    </FormField>
                    <FormField label={t(hint.dosageLabel)} htmlFor="dosage" required>
                        <Input
                            id="dosage"
                            name="dosage"
                            value={form.dosage}
                            onChange={handleChange}
                            placeholder={t(hint.dosagePlaceholder)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Quantité')} htmlFor="quantity" required>
                        <Input
                            id="quantity"
                            name="quantity"
                            value={form.quantity}
                            onChange={handleChange}
                            placeholder={t(hint.quantityPlaceholder)}
                            required
                        />
                    </FormField>
                    <FormField label={t('Prises')}>
                        <div className="dossier-form__checkboxes">
                            <label>
                                <input type="checkbox" name="morning" checked={form.morning} onChange={handleChange} /> {t('Matin')}
                            </label>
                            <label>
                                <input type="checkbox" name="noon" checked={form.noon} onChange={handleChange} /> {t('Midi')}
                            </label>
                            <label>
                                <input type="checkbox" name="evening" checked={form.evening} onChange={handleChange} /> {t('Soir')}
                            </label>
                        </div>
                    </FormField>
                    <FormField label={t('Instructions')} htmlFor="instructions">
                        <Textarea
                            id="instructions"
                            name="instructions"
                            rows={3}
                            value={form.instructions}
                            onChange={handleChange}
                            fullWidth
                        />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                        {t('Annuler')}
                    </Button>
                    <Button
                        type="submit"
                        variant="primary"
                        disabled={isLoading || !form.medicationId}
                    >
                        {isLoading ? <Spinner size="small" /> : t('Ajouter')}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
