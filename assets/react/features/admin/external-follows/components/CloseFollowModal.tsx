import React, { useState } from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Form } from '@/react/components/Forms/Form';
import { FormField } from '@/react/components/Forms/FormField';
import { Textarea } from '@/react/components/Forms/Textarea';
import { Button } from '@/react/components/UI/Button';
import { Alert } from '@/react/components/UI/Alert';
import { ExternalFollowInvitation } from '../types/types';
import { closeMyExternalFollow } from '../services/externalFollowsService';

interface CloseFollowModalProps {
    isOpen: boolean;
    follow: ExternalFollowInvitation;
    onClose: () => void;
    onSuccess?: () => void;
}

export function CloseFollowModal({ isOpen, follow, onClose, onSuccess }: CloseFollowModalProps) {
    const [reason, setReason] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError(null);
        try {
            await closeMyExternalFollow(follow.id, reason.trim());
            onSuccess?.();
            setReason('');
            onClose();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la fermeture du suivi.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Fermer mon suivi externe" size="medium">
            <div className="external-follow-form">
                {error && <Alert variant="error">{error}</Alert>}
                <p className="external-follow-form__intro">
                    Vous allez mettre fin à votre suivi du patient{' '}
                    <strong>{follow.patientName}</strong> (organisation{' '}
                    {follow.organizationName}). Votre accès au dossier sera révoqué.
                    L'organisation d'origine du patient sera notifiée par email du motif.
                </p>
                <Form onSubmit={handleSubmit}>
                    <FormField label="Motif de la fermeture *">
                        <Textarea
                            value={reason}
                            onChange={(e) => setReason(e.target.value)}
                            placeholder="Indiquez pourquoi vous fermez ce suivi..."
                            rows={4}
                            required
                        />
                    </FormField>
                    <div className="external-follow-form__actions">
                        <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
                        <Button type="submit" variant="danger" disabled={isSubmitting || reason.trim().length < 3}>
                            {isSubmitting ? 'Fermeture...' : 'Fermer mon suivi'}
                        </Button>
                    </div>
                </Form>
            </div>
        </Modal>
    );
}