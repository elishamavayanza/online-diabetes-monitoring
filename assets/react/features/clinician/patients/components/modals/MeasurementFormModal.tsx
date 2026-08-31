import { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Input } from '@/react/components/Forms/Input';
import { Select } from '@/react/components/Forms/Select';
import { FormField } from '@/react/components/Forms/FormField';
import { Alert } from '@/react/components/UI/Alert';
import { Spinner } from '@/react/components/UI/Spinner';
import { MeasurementTypeId } from '../../types';
import { MEASUREMENT_TYPES } from '../../config/measurementTypes';
import { createMeasurement } from '../../services/dossierActionsService';

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
    const [step, setStep] = useState<'type' | 'form'>('type');
    const [type, setType] = useState<MeasurementTypeId | null>(initialType ?? null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState<Record<string, string>>({});

    useEffect(() => {
        if (!isOpen) {
            setStep(initialType ? 'form' : 'type');
            setType(initialType ?? null);
            setForm({});
            setError(null);
        } else if (initialType) {
            setType(initialType);
            setStep('form');
        }
    }, [isOpen, initialType]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
    };

    const handleSelectType = (selected: MeasurementTypeId) => {
        setType(selected);
        setStep('form');
        setForm({});
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!type) return;
        setIsLoading(true);
        setError(null);
        try {
            let payload: Record<string, unknown> = { ...form };
            if (type === 'physicalActivity') {
                payload = { ...form, durationMinutes: Number(form.durationMinutes) };
            }
            if (type === 'bloodGlucose' && !form.unit) {
                payload.unit = 'MG_DL';
            }
            if (type === 'hba1c' && form.measuredAt) {
                payload.measuredAt = new Date(form.measuredAt).toISOString();
            }
            await createMeasurement(patientId, type, payload);
            onSuccess();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors du prélèvement.');
        } finally {
            setIsLoading(false);
        }
    };

    const renderFormFields = () => {
        switch (type) {
            case 'bloodGlucose':
                return (
                    <>
                        <FormField label="Valeur" htmlFor="value" required>
                            <Input id="value" name="value" type="number" step="0.01" value={form.value ?? ''} onChange={handleChange} required />
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
                        <FormField label="Systolique (mmHg)" htmlFor="systolic" required>
                            <Input id="systolic" name="systolic" type="number" value={form.systolic ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Diastolique (mmHg)" htmlFor="diastolic" required>
                            <Input id="diastolic" name="diastolic" type="number" value={form.diastolic ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Pouls (optionnel)" htmlFor="pulse">
                            <Input id="pulse" name="pulse" type="number" value={form.pulse ?? ''} onChange={handleChange} />
                        </FormField>
                    </>
                );
            case 'hba1c':
                return (
                    <>
                        <FormField label="HbA1c (%)" htmlFor="valuePercent" required>
                            <Input id="valuePercent" name="valuePercent" type="number" step="0.1" value={form.valuePercent ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Date de mesure" htmlFor="measuredAt">
                            <Input id="measuredAt" name="measuredAt" type="datetime-local" value={form.measuredAt ?? ''} onChange={handleChange} />
                        </FormField>
                    </>
                );
            case 'weight':
                return (
                    <>
                        <FormField label="Poids (kg)" htmlFor="valueKg" required>
                            <Input id="valueKg" name="valueKg" type="number" step="0.1" value={form.valueKg ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Taille (cm)" htmlFor="heightCm">
                            <Input id="heightCm" name="heightCm" type="number" step="0.1" value={form.heightCm ?? ''} onChange={handleChange} />
                        </FormField>
                    </>
                );
            case 'physicalActivity':
                return (
                    <>
                        <FormField label="Type d'activité" htmlFor="activityType" required>
                            <Input id="activityType" name="activityType" value={form.activityType ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Durée (minutes)" htmlFor="durationMinutes" required>
                            <Input id="durationMinutes" name="durationMinutes" type="number" min="1" value={form.durationMinutes ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Calories brûlées" htmlFor="caloriesBurned">
                            <Input id="caloriesBurned" name="caloriesBurned" type="number" value={form.caloriesBurned ?? ''} onChange={handleChange} />
                        </FormField>
                    </>
                );
            case 'laboratory':
                return (
                    <>
                        <FormField label="Nom de l'examen" htmlFor="testName" required>
                            <Input id="testName" name="testName" value={form.testName ?? ''} onChange={handleChange} required />
                        </FormField>
                        <FormField label="Laboratoire" htmlFor="labName">
                            <Input id="labName" name="labName" value={form.labName ?? ''} onChange={handleChange} />
                        </FormField>
                        <FormField label="URL du fichier" htmlFor="fileUrl">
                            <Input id="fileUrl" name="fileUrl" type="url" value={form.fileUrl ?? ''} onChange={handleChange} />
                        </FormField>
                    </>
                );
            default:
                return null;
        }
    };

    const typeLabel = type ? MEASUREMENT_TYPES.find((t) => t.id === type)?.label : '';

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
                <form onSubmit={handleSubmit} className="dossier-form">
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
