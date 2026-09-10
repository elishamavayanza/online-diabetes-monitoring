import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { Insulin, MeasurementTypeId, PrescriptionItem } from '../../../types';
import { MEASUREMENT_TYPES } from '../../../config/measurementTypes';
import { PHYSICAL_ACTIVITY_OPTIONS } from '../../../config/physicalActivities';
import { useMeasurementForm } from '@/react/features/clinician/patients/hooks/useMeasurementForm';
import {
    fetchInsulins,
    fetchPatientPrescriptionItems,
} from '@/react/features/clinician/patients/services/insulinInjectionService';
import { useI18n } from '@/react/i18n/I18nContext';

interface MeasurementFormModalProps {
    isOpen: boolean;
    onClose: () => void;
    patientId: string;
    initialType?: MeasurementTypeId;
    onSuccess: () => void;
}

const GLUCOSE_CONTEXT_OPTIONS = [
    { value: 'FASTING', label: 'À jeun' },
    { value: 'BEFORE_MEAL', label: 'Avant repas' },
    { value: 'AFTER_MEAL', label: 'Après repas' },
    { value: 'BEDTIME', label: 'Coucher' },
    { value: 'RANDOM', label: 'Aléatoire' },
];

const GLUCOSE_UNIT_OPTIONS = [
    { value: 'MG_DL', label: 'mg/dL' },
    { value: 'MMOL_L', label: 'mmol/L' },
];

const INJECTION_SITE_OPTIONS = [
    { value: 'ABDOMEN', label: 'Abdomen' },
    { value: 'THIGH', label: 'Cuisse' },
    { value: 'UPPER_ARM', label: 'Haut du bras' },
    { value: 'BUTTOCK', label: 'Fesse' },
    { value: 'OTHER', label: 'Autre' },
];

const INJECTION_STATUS_OPTIONS = [
    { value: 'TAKEN', label: 'Réalisée' },
    { value: 'SKIPPED', label: 'Sautée' },
    { value: 'DELAYED', label: 'En retard' },
];

interface InsulinOptionsState {
    insulins: Insulin[];
    prescriptionItems: PrescriptionItem[];
    loading: boolean;
    error: string | null;
}

export function MeasurementFormModal({
                                         isOpen,
                                         onClose,
                                         patientId,
                                         initialType,
                                         onSuccess,
                                     }: MeasurementFormModalProps) {
    const { t } = useI18n();
    const {
        step,
        type,
        isLoading,
        error,
        form,
        handleChange,
        handleSelectType,
        handleSubmit,
        setStep,
    } = useMeasurementForm({ isOpen, onClose, patientId, initialType, onSuccess });

    const typeLabel = type ? (MEASUREMENT_TYPES.find((t) => t.id === type)?.label ?? '') : '';
    const [labFile, setLabFile] = useState<File | null>(null);
    const [insulinOptions, setInsulinOptions] = useState<InsulinOptionsState>({
        insulins: [],
        prescriptionItems: [],
        loading: false,
        error: null,
    });

    useEffect(() => {
        if (!isOpen || type !== 'insulinInjection') return;

        let cancelled = false;
        setInsulinOptions((prev) => ({ ...prev, loading: true, error: null }));

        Promise.all([fetchInsulins(), fetchPatientPrescriptionItems(patientId)])
            .then(([insulins, prescriptionItems]) => {
                if (cancelled) return;
                setInsulinOptions({ insulins, prescriptionItems, loading: false, error: null });
            })
            .catch((err) => {
                if (cancelled) return;
                const message = err instanceof Error ? err.message : t('Erreur lors du chargement des insulines.');
                setInsulinOptions((prev) => ({ ...prev, loading: false, error: message }));
            });

        return () => {
            cancelled = true;
        };
    }, [isOpen, type, patientId]);

    // Présélectionne automatiquement le premier élément prescrit compatible avec une insuline,
    // puis l'insuline correspondante.
    useEffect(() => {
        if (type !== 'insulinInjection' || insulinOptions.loading) return;

        const { insulins, prescriptionItems } = insulinOptions;
        const injectableItems = prescriptionItems.filter((item) =>
            insulins.some((ins) => ins.medicationId === item.medicationId),
        );
        if (injectableItems.length === 0) return;

        let itemId = form.prescriptionItemId ?? '';
        if (!injectableItems.some((item) => item.id === itemId)) {
            itemId = injectableItems[0].id;
            handleChange({ target: { name: 'prescriptionItemId', value: itemId } } as React.ChangeEvent<HTMLSelectElement>);
        }

        const selectedItem = injectableItems.find((item) => item.id === itemId);
        const matchedInsulins = insulins.filter((ins) => ins.medicationId === selectedItem?.medicationId);
        if (matchedInsulins.length > 0 && !matchedInsulins.some((ins) => ins.id === form.insulinId)) {
            handleChange({ target: { name: 'insulinId', value: matchedInsulins[0].id } } as React.ChangeEvent<HTMLSelectElement>);
        }
    }, [type, insulinOptions, form.prescriptionItemId, form.insulinId, handleChange]);

    const renderDateTimeField = () => (
        <FormField label={t('Date et heure')} htmlFor="measuredAt" required>
            <Input
                id="measuredAt"
                name="measuredAt"
                type="datetime-local"
                value={form.measuredAt ?? ''}
                onChange={handleChange}
                required
            />
        </FormField>
    );

    const renderFormFields = () => {
        switch (type) {
            case 'bloodGlucose':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t('Valeur')} htmlFor="value" required>
                            <Input id="value" name="value" type="number" step="0.01" value={form.value ?? ''} onChange={handleChange} placeholder={t('Ex : 1.26')} required />
                        </FormField>
                        <FormField label={t('Unité')} htmlFor="unit" required>
                            <Select id="unit" name="unit" value={form.unit ?? 'MG_DL'} onChange={handleChange} options={GLUCOSE_UNIT_OPTIONS} />
                        </FormField>
                        <FormField label={t('Contexte')} htmlFor="context" required>
                            <Select id="context" name="context" value={form.context ?? 'FASTING'} onChange={handleChange} options={GLUCOSE_CONTEXT_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))} />
                        </FormField>
                    </>
                );
            case 'bloodPressure':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t('Systolique (mmHg)')} htmlFor="systolic" required>
                            <Input id="systolic" name="systolic" type="number" value={form.systolic ?? ''} onChange={handleChange} placeholder={t('Ex : 120')} required />
                        </FormField>
                        <FormField label={t('Diastolique (mmHg)')} htmlFor="diastolic" required>
                            <Input id="diastolic" name="diastolic" type="number" value={form.diastolic ?? ''} onChange={handleChange} placeholder={t('Ex : 80')} required />
                        </FormField>
                        <FormField label={t('Pouls (optionnel)')} htmlFor="pulse">
                            <Input id="pulse" name="pulse" type="number" value={form.pulse ?? ''} onChange={handleChange} placeholder={t('Ex : 72')} />
                        </FormField>
                    </>
                );
            case 'hba1c':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t('HbA1c (%)')} htmlFor="valuePercent" required>
                            <Input id="valuePercent" name="valuePercent" type="number" step="0.1" value={form.valuePercent ?? ''} onChange={handleChange} placeholder={t('Ex : 6.5')} required />
                        </FormField>
                    </>
                );
            case 'weight':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t('Poids (kg)')} htmlFor="valueKg" required>
                            <Input id="valueKg" name="valueKg" type="number" step="0.1" value={form.valueKg ?? ''} onChange={handleChange} placeholder={t('Ex : 75.50')} required />
                        </FormField>
                        <FormField label={t('Taille (cm)')} htmlFor="heightCm">
                            <Input id="heightCm" name="heightCm" type="number" step="0.1" value={form.heightCm ?? ''} onChange={handleChange} placeholder={t('Ex : 175.00')} />
                        </FormField>
                    </>
                );
            case 'physicalActivity': {
                const isOther = form.activityType === 'OTHER';
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t("Type d'activité")} htmlFor="activityType" required>
                            <Select id="activityType" name="activityType" value={form.activityType ?? 'WALKING'} onChange={handleChange} options={PHYSICAL_ACTIVITY_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))} required />
                        </FormField>
                        {isOther && (
                            <FormField label={t("Préciser l'activité")} htmlFor="customActivity" required>
                                <Input id="customActivity" name="activityType" value={form.activityType === 'OTHER' ? '' : form.activityType} onChange={handleChange} placeholder={t('Ex : Randonnée, Tennis...')} required />
                            </FormField>
                        )}
                        <FormField label={t('Durée (minutes)')} htmlFor="durationMinutes" required>
                            <Input id="durationMinutes" name="durationMinutes" type="number" min="1" value={form.durationMinutes ?? ''} onChange={handleChange} placeholder={t('Ex : 30')} required />
                        </FormField>
                        <FormField label={t('Calories brûlées')} htmlFor="caloriesBurned">
                            <Input id="caloriesBurned" name="caloriesBurned" type="number" value={form.caloriesBurned ?? ''} onChange={handleChange} placeholder={t('Ex : 300')} />
                        </FormField>
                    </>
                );
            }
            case 'laboratory': {
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label={t("Nom de l'examen")} htmlFor="testName" required>
                            <Input id="testName" name="testName" value={form.testName ?? ''} onChange={handleChange} placeholder={t('Ex : Bilan lipidique complet')} required />
                        </FormField>
                        <FormField label={t('Laboratoire')} htmlFor="labName">
                            <Input id="labName" name="labName" value={form.labName ?? ''} onChange={handleChange} placeholder={t('Ex : Laboratoire Central Goma')} />
                        </FormField>
                        <FormField label={t('Fichier du résultat')} htmlFor="labFile">
                            <FileUpload
                                accept=".pdf,.doc,.docx,.jpg,.png"
                                multiple={false}
                                maxFiles={1}
                                maxSizeInMB={10}
                                onFilesSelected={(files) => {
                                    if (files.length > 0) {
                                        setLabFile(files[0]);
                                    }
                                }}
                                label={t('Cliquez ou déposez le fichier ici')}
                                hint={t('PDF, Word ou image (max 10 Mo)')}
                            />
                        </FormField>
                    </>
                );
            }
            case 'insulinInjection': {
                const { insulins, prescriptionItems, loading, error } = insulinOptions;
                const injectableItems = prescriptionItems.filter((item) =>
                    insulins.some((ins) => ins.medicationId === item.medicationId),
                );
                const selectedItem = injectableItems.find((item) => item.id === form.prescriptionItemId);
                const matchedInsulins = insulins.filter((ins) => ins.medicationId === selectedItem?.medicationId);

                if (loading) {
                    return (
                        <div className="injection-options-loading">
                            <Spinner size="small" />
                            {t('Chargement des insulines et prescriptions…')}
                        </div>
                    );
                }
                if (error) {
                    return <Alert variant="error">{error}</Alert>;
                }
                if (injectableItems.length === 0) {
                    return (
                        <p>
                            {t("Aucun médicament insulinique prescrit trouvé. Ajoutez d'abord une prescription d'insuline avant d'enregistrer une injection.")}
                        </p>
                    );
                }

                return (
                    <>
                        <FormField label={t('Date et heure')} htmlFor="injectedAt" required>
                            <Input
                                id="injectedAt"
                                name="injectedAt"
                                type="datetime-local"
                                value={form.injectedAt ?? ''}
                                onChange={handleChange}
                                required
                            />
                        </FormField>
                        <FormField label={t('Médicament prescrit')} htmlFor="prescriptionItemId" required>
                            <Select
                                id="prescriptionItemId"
                                name="prescriptionItemId"
                                value={form.prescriptionItemId ?? ''}
                                onChange={handleChange}
                                options={injectableItems.map((item) => ({
                                    value: item.id,
                                    label: `${item.medicationName ?? t('Insuline')}${item.dosage ? ` — ${item.dosage}` : ''}`,
                                }))}
                                required
                            />
                        </FormField>
                        <FormField label={t('Insuline')} htmlFor="insulinId" required>
                            <Select
                                id="insulinId"
                                name="insulinId"
                                value={form.insulinId ?? matchedInsulins[0]?.id ?? ''}
                                onChange={handleChange}
                                options={matchedInsulins.map((ins) => ({
                                    value: ins.id,
                                    label: `${ins.medicationName ?? ins.id} — ${ins.insulinType}${ins.concentration ? ` (${ins.concentration})` : ''}`,
                                }))}
                                required
                            />
                        </FormField>
                        <FormField label={t('Dose (unités)')} htmlFor="doseUnits" required>
                            <Input
                                id="doseUnits"
                                name="doseUnits"
                                type="number"
                                step="0.5"
                                min="0.5"
                                value={form.doseUnits ?? ''}
                                onChange={handleChange}
                                placeholder={t('Ex : 12')}
                                required
                            />
                        </FormField>
                        <FormField label={t("Site d'injection")} htmlFor="injectionSite" required>
                            <Select
                                id="injectionSite"
                                name="injectionSite"
                                value={form.injectionSite ?? 'ABDOMEN'}
                                onChange={handleChange}
                                options={INJECTION_SITE_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))}
                            />
                        </FormField>
                        <FormField label={t('Statut')} htmlFor="status" required>
                            <Select
                                id="status"
                                name="status"
                                value={form.status ?? 'TAKEN'}
                                onChange={handleChange}
                                options={INJECTION_STATUS_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))}
                            />
                        </FormField>
                        <FormField label={t('Notes')} htmlFor="notes">
                            <Input
                                id="notes"
                                name="notes"
                                value={form.notes ?? ''}
                                onChange={handleChange}
                                placeholder={t('Observations éventuelles')}
                            />
                        </FormField>
                    </>
                );
            }
            default:
                return null;
        }
    };

    const handleFormSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        handleSubmit(e, labFile);
    };

    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            title={step === 'type' ? t('Nouveau prélèvement') : t('Prélèvement — {{ type }}', { type: t(typeLabel) })}
        >
            {error && <Alert variant="error">{error}</Alert>}

            {step === 'type' ? (
                <div className="measurement-type-picker">
                    {MEASUREMENT_TYPES.map((item) => (
                        <button
                            key={item.id}
                            type="button"
                            className="measurement-type-picker__item"
                            onClick={() => handleSelectType(item.id)}
                        >
                            <span className="measurement-type-picker__icon">{item.icon}</span>
                            <span className="measurement-type-picker__label">{t(item.label)}</span>
                            <span className="measurement-type-picker__desc">{t(item.description)}</span>
                        </button>
                    ))}
                </div>
            ) : (
                <form onSubmit={handleFormSubmit} className="dossier-form">
                    <div className="dossier-form__grid">{renderFormFields()}</div>
                    <div className="dossier-form__actions">
                        {!initialType && (
                            <Button type="button" variant="secondary" onClick={() => setStep('type')}>
                                {t('Retour')}
                            </Button>
                        )}
                        <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                            {t('Annuler')}
                        </Button>
                        <Button type="submit" variant="primary" disabled={isLoading}>
                            {isLoading ? <Spinner size="small" /> : t('Enregistrer')}
                        </Button>
                    </div>
                </form>
            )}
        </Modal>
    );
}
