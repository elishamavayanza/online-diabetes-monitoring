import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { useI18n } from '@/react/i18n/I18nContext';

interface ReactivateModalProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    message: string;
    confirmLabel?: string;
    onConfirm: () => Promise<void>;
}

export function ReactivateModal({
    isOpen,
    onClose,
    title,
    message,
    confirmLabel = 'Réactiver',
    onConfirm,
}: ReactivateModalProps) {
    const { t } = useI18n();
    const [error, setError] = useState<string | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setIsSubmitting(true);
        try {
            await onConfirm();
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : t('Une erreur est survenue lors de la réactivation.'));
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose}>
            <div className="suspend-modal">
                <h2>{title}</h2>
                <p className="suspend-modal__entity">{message}</p>
                {error && <Alert variant="error">{error}</Alert>}
                <form onSubmit={handleSubmit} className="suspend-modal__actions">
                    <Button type="button" variant="outline" onClick={onClose}>
                        {t('Annuler')}
                    </Button>
                    <Button type="submit" variant="success" isLoading={isSubmitting}>
                        {isSubmitting ? t('Réactivation...') : t(confirmLabel)}
                    </Button>
                </form>
            </div>
        </Modal>
    );
}