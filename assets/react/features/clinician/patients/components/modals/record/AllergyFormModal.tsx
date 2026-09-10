import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientAllergy, PatientDossierData } from '../../../types';
import { useAllergyForm } from "@/react/features/clinician/patients/hooks/record/useAllergyForm";
import { useI18n } from '@/react/i18n/I18nContext';

interface AllergyFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    allergy?: PatientAllergy;
    onSuccess: () => void;
}

const SEVERITY_OPTIONS = [
    { value: 'MILD', label: 'Légère' },
    { value: 'MODERATE', label: 'Modérée' },
    { value: 'SEVERE', label: 'Sévère' },
];

export function AllergyFormModal({ isOpen, onClose, data, allergy, onSuccess }: AllergyFormModalProps) {
    const { t } = useI18n();
    const { form, handleChange, handleSubmit, isLoading, error, isEdit } = useAllergyForm({
        isOpen,
        data,
        allergy,
        onSuccess,
        onClose,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? t("Modifier l'allergie") : t('Ajouter une allergie')}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label={t('Allergène')} htmlFor="name" required>
                        <Input
                            id="name"
                            name="name"
                            value={form.name}
                            onChange={handleChange}
                            placeholder={t('Ex : Pénicilline, Arachide...')}
                            required
                        />
                    </FormField>
                    <FormField label={t('Sévérité')} htmlFor="severity" required>
                        <Select
                            id="severity"
                            name="severity"
                            value={form.severity}
                            onChange={handleChange}
                            options={SEVERITY_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))}
                        />
                    </FormField>
                    <FormField label={t('Date du diagnostic')} htmlFor="diagnosedAt" required>
                        <Input
                            id="diagnosedAt"
                            name="diagnosedAt"
                            type="datetime-local"
                            value={form.diagnosedAt}
                            onChange={handleChange}
                            required
                        />
                    </FormField>
                    <FormField label={t('Réaction')} htmlFor="reaction">
                        <Input
                            id="reaction"
                            name="reaction"
                            value={form.reaction}
                            onChange={handleChange}
                            placeholder={t('Ex : Éruption cutanée, œdème...')}
                        />
                    </FormField>
                    <FormField label={t('Notes')} htmlFor="notes" className="dossier-form__field--full">
                        <Textarea
                            id="notes"
                            name="notes"
                            rows={3}
                            value={form.notes}
                            onChange={handleChange}
                            placeholder={t('Ex : Éviter tout contact, porter un bracelet...')}
                            fullWidth
                        />
                    </FormField>
                </div>
                <div className="dossier-form__actions">
                    <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>{t('Annuler')}</Button>
                    <Button type="submit" variant="primary" disabled={isLoading}>
                        {isLoading ? <Spinner size="small" /> : isEdit ? t('Enregistrer') : t('Ajouter')}
                    </Button>
                </div>
            </form>
        </Modal>
    );
}
