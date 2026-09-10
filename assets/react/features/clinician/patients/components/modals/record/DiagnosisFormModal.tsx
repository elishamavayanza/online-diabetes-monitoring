import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { PatientDiagnosis, PatientDossierData } from '../../../types';
import {useDiagnosisForm} from "@/react/features/clinician/patients/hooks/record/useDiagnosisForm";
import { useI18n } from '@/react/i18n/I18nContext';

interface DiagnosisFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    data: PatientDossierData;
    diagnosis?: PatientDiagnosis;
    onSuccess: () => void;
}

const STATUS_OPTIONS = [
    { value: 'CONFIRMED', label: 'Confirmé' },
    { value: 'SUSPECTED', label: 'Suspecté' },
    { value: 'RULED_OUT', label: 'Écarté' },
    { value: 'IN_REMISSION', label: 'En rémission' },
];

export function DiagnosisFormModal({
                                       isOpen,
                                       onClose,
                                       data,
                                       diagnosis,
                                       onSuccess,
                                   }: DiagnosisFormModalProps) {
    const { t } = useI18n();
    const { form, handleChange, handleSubmit, isLoading, error, isEdit } = useDiagnosisForm({
        isOpen,
        onClose,
        data,
        diagnosis,
        onSuccess,
    });

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={isEdit ? t('Modifier le diagnostic') : t('Ajouter un diagnostic')}>
            {error && <Alert variant="error">{error}</Alert>}
            <form onSubmit={handleSubmit} className="dossier-form">
                <div className="dossier-form__grid">
                    <FormField label={t('Affection')} htmlFor="conditionName" required>
                        <Input
                            id="conditionName"
                            name="conditionName"
                            value={form.conditionName}
                            onChange={handleChange}
                            placeholder={t('Ex : Diabète de type 2, Hypertension...')}
                            required
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
                    <FormField label={t('Description')} htmlFor="description" className="dossier-form__field--full">
                        <Textarea
                            id="description"
                            name="description"
                            rows={4}
                            value={form.description}
                            onChange={handleChange}
                            placeholder={t("Ex : Découverte fortuite lors d'un bilan sanguin...")}
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
