import { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { FileUpload } from '@/react/components/Forms/FileUpload';
import { MeasurementTypeId } from '../../types';
import { MEASUREMENT_TYPES } from '../../config/measurementTypes';
import { PHYSICAL_ACTIVITY_OPTIONS } from '../../config/physicalActivities';
import { useMeasurementForm } from '@/react/features/clinician/patients/hooks/useMeasurementForm';

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

export function MeasurementFormModal({
                                         isOpen,
                                         onClose,
                                         patientId,
                                         initialType,
                                         onSuccess,
                                     }: MeasurementFormModalProps) {
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

    const typeLabel = type ? MEASUREMENT_TYPES.find((t) => t.id === type)?.label : '';
    const [labFile, setLabFile] = useState<File | null>(null);

    const renderDateTimeField = () => (
        <FormField label="Date et heure" htmlFor="measuredAt" required>
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
                        <FormField label="Valeur" htmlFor="value" required>
                            <Input id="value" name="value" type="number" step="0.01" value={form.value ?? ''} onChange={handleChange} placeholder="Ex : 1.26" required />
                        </FormField>
                        <FormField label="Unité" htmlFor="unit" required>
                            <Select id="unit" name="unit" value={form.unit ?? 'MG_DL'} onChange={handleChange} options={GLUCOSE_UNIT_OPTIONS} />
                        </FormField>
                        <FormField label="Contexte" htmlFor="context" required>
                            <Select id="context" name="context" value={form.context ?? 'FASTING'} onChange={handleChange} options={GLUCOSE_CONTEXT_OPTIONS} />
                        </FormField>
                    </>
                );
            case 'bloodPressure':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label="Systolique (mmHg)" htmlFor="systolic" required>
                            <Input id="systolic" name="systolic" type="number" value={form.systolic ?? ''} onChange={handleChange} placeholder="Ex : 120" required />
                        </FormField>
                        <FormField label="Diastolique (mmHg)" htmlFor="diastolic" required>
                            <Input id="diastolic" name="diastolic" type="number" value={form.diastolic ?? ''} onChange={handleChange} placeholder="Ex : 80" required />
                        </FormField>
                        <FormField label="Pouls (optionnel)" htmlFor="pulse">
                            <Input id="pulse" name="pulse" type="number" value={form.pulse ?? ''} onChange={handleChange} placeholder="Ex : 72" />
                        </FormField>
                    </>
                );
            case 'hba1c':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label="HbA1c (%)" htmlFor="valuePercent" required>
                            <Input id="valuePercent" name="valuePercent" type="number" step="0.1" value={form.valuePercent ?? ''} onChange={handleChange} placeholder="Ex : 6.5" required />
                        </FormField>
                    </>
                );
            case 'weight':
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label="Poids (kg)" htmlFor="valueKg" required>
                            <Input id="valueKg" name="valueKg" type="number" step="0.1" value={form.valueKg ?? ''} onChange={handleChange} placeholder="Ex : 75.50" required />
                        </FormField>
                        <FormField label="Taille (cm)" htmlFor="heightCm">
                            <Input id="heightCm" name="heightCm" type="number" step="0.1" value={form.heightCm ?? ''} onChange={handleChange} placeholder="Ex : 175.00" />
                        </FormField>
                    </>
                );
            case 'physicalActivity': {
                const isOther = form.activityType === 'OTHER';
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label="Type d'activité" htmlFor="activityType" required>
                            <Select id="activityType" name="activityType" value={form.activityType ?? 'WALKING'} onChange={handleChange} options={PHYSICAL_ACTIVITY_OPTIONS} required />
                        </FormField>
                        {isOther && (
                            <FormField label="Préciser l'activité" htmlFor="customActivity" required>
                                <Input id="customActivity" name="activityType" value={form.activityType === 'OTHER' ? '' : form.activityType} onChange={handleChange} placeholder="Ex : Randonnée, Tennis..." required />
                            </FormField>
                        )}
                        <FormField label="Durée (minutes)" htmlFor="durationMinutes" required>
                            <Input id="durationMinutes" name="durationMinutes" type="number" min="1" value={form.durationMinutes ?? ''} onChange={handleChange} placeholder="Ex : 30" required />
                        </FormField>
                        <FormField label="Calories brûlées" htmlFor="caloriesBurned">
                            <Input id="caloriesBurned" name="caloriesBurned" type="number" value={form.caloriesBurned ?? ''} onChange={handleChange} placeholder="Ex : 300" />
                        </FormField>
                    </>
                );
            }
            case 'laboratory': {
                return (
                    <>
                        {renderDateTimeField()}
                        <FormField label="Nom de l'examen" htmlFor="testName" required>
                            <Input id="testName" name="testName" value={form.testName ?? ''} onChange={handleChange} placeholder="Ex : Bilan lipidique complet" required />
                        </FormField>
                        <FormField label="Laboratoire" htmlFor="labName">
                            <Input id="labName" name="labName" value={form.labName ?? ''} onChange={handleChange} placeholder="Ex : Laboratoire Central Goma" />
                        </FormField>
                        <FormField label="Fichier du résultat" htmlFor="labFile">
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
                                label="Cliquez ou déposez le fichier ici"
                                hint="PDF, Word ou image (max 10 Mo)"
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
            title={step === 'type' ? 'Nouveau prélèvement' : `Prélèvement — ${typeLabel}`}
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
                            <span className="measurement-type-picker__label">{item.label}</span>
                            <span className="measurement-type-picker__desc">{item.description}</span>
                        </button>
                    ))}
                </div>
            ) : (
                <form onSubmit={handleFormSubmit} className="dossier-form">
                    <div className="dossier-form__grid">{renderFormFields()}</div>
                    <div className="dossier-form__actions">
                        {!initialType && (
                            <Button type="button" variant="secondary" onClick={() => setStep('type')}>
                                Retour
                            </Button>
                        )}
                        <Button type="button" variant="secondary" onClick={onClose} disabled={isLoading}>
                            Annuler
                        </Button>
                        <Button type="submit" variant="primary" disabled={isLoading}>
                            {isLoading ? <Spinner size="small" /> : 'Enregistrer'}
                        </Button>
                    </div>
                </form>
            )}
        </Modal>
    );
}
