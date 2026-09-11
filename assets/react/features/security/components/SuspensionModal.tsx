import React, { useEffect, useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Select } from '@/react/components/Forms/Select';
import { Input } from '@/react/components/Forms/Input';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { SuspensionPayload } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

const DURATION_OPTIONS = [
    { value: '15', label: '15 jours' },
    { value: '30', label: '30 jours' },
    { value: '60', label: '60 jours' },
    { value: '90', label: '90 jours' },
    { value: 'indefinite', label: 'Indéterminée (levée manuelle)' },
    { value: 'custom', label: 'Période personnalisée' },
];

interface SuspensionModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    entityLabel: string;
    onConfirm: (payload: SuspensionPayload) => Promise<void>;
}

export function SuspensionModal({
    isOpen,
    onClose,
    title,
    entityLabel,
    onConfirm,
}: SuspensionModalProps) {
    const { t } = useI18n();
    const [reason, setReason] = useState('');
    const [duration, setDuration] = useState('indefinite');
    const [startValue, setStartValue] = useState('');
    const [endValue, setEndValue] = useState('');
    const [error, setError] = useState<string | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        if (isOpen) {
            setReason('');
            setDuration('indefinite');
            setStartValue('');
            setEndValue('');
            setError(null);
        }
    }, [isOpen]);

    const toIso = (value: string): string | undefined => {
        if (!value) return undefined;
        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? undefined : parsed.toISOString();
    };

    const buildPayload = (): SuspensionPayload | null => {
        if (!reason.trim()) {
            setError(t('Veuillez indiquer le motif de la suspension.'));
            return null;
        }
        if (reason.length > 500) {
            setError(t('Le motif ne peut pas dépasser 500 caractères.'));
            return null;
        }

        if (duration === 'custom') {
            const startIso = toIso(startValue);
            const endIso = toIso(endValue);
            if (!endIso) {
                setError(t('Veuillez indiquer la date de fin de la suspension.'));
                return null;
            }
            const end = new Date(endIso);
            const start = startIso ? new Date(startIso) : new Date();
            if (end <= start) {
                setError(t('La date de fin doit être postérieure à la date de début.'));
                return null;
            }
            return { reason: reason.trim(), startsAt: startIso ?? start.toISOString(), endsAt: endIso };
        }

        if (duration === 'indefinite') {
            return { reason: reason.trim() };
        }

        return { reason: reason.trim(), durationDays: Number(duration) };
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        const payload = buildPayload();
        if (!payload) return;
        setIsSubmitting(true);
        try {
            await onConfirm(payload);
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : t('Une erreur est survenue lors de la suspension.'));
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="suspend-modal">
                <h2>{title}</h2>
                <p className="suspend-modal__entity">
                    {entityLabel}
                </p>
                {error && <Alert variant="error">{error}</Alert>}
                <Form onSubmit={handleSubmit}>
                    <FormField label={t('Motif de la suspension *')} htmlFor="suspension-reason">
                        <Textarea
                            id="suspension-reason"
                            value={reason}
                            onChange={(e) => setReason(e.target.value)}
                            placeholder={t('Expliquez la raison de la suspension (manquement, enquête...)')}
                            rows={4}
                            required
                            fullWidth
                        />
                    </FormField>

                    <FormField label={t('Durée')} htmlFor="suspension-duration">
                        <Select
                            id="suspension-duration"
                            value={duration}
                            onChange={(e) => setDuration(e.target.value)}
                            options={DURATION_OPTIONS.map((option) => ({ ...option, label: t(option.label) }))}
                            fullWidth
                        />
                    </FormField>

                    {duration === 'custom' && (
                        <div className="suspend-modal__custom">
                            <FormField label={t('Date de début')} htmlFor="suspension-start">
                                <Input
                                    id="suspension-start"
                                    type="datetime-local"
                                    value={startValue}
                                    onChange={(e) => setStartValue(e.target.value)}
                                    fullWidth
                                />
                            </FormField>
                            <FormField label={t('Date de fin *')} htmlFor="suspension-end">
                                <Input
                                    id="suspension-end"
                                    type="datetime-local"
                                    value={endValue}
                                    onChange={(e) => setEndValue(e.target.value)}
                                    fullWidth
                                />
                            </FormField>
                        </div>
                    )}

                    <div className="suspend-modal__actions">
                        <Button type="button" variant="outline" onClick={onClose}>
                            {t('Annuler')}
                        </Button>
                        <Button type="submit" variant="danger" isLoading={isSubmitting}>
                            {isSubmitting ? t('Suspension...') : t('Suspendre')}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}