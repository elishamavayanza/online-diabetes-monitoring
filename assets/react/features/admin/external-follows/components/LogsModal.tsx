import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useExternalFollowLogs } from '../hooks/useExternalFollowLogs';

interface LogsModalProps {
    isOpen: boolean;
    organizationId: string;
    invitationId: string;
    patientName: string;
    professionalName: string;
    onClose: () => void;
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    const date = new Date(value);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function LogsModal({ isOpen, organizationId, invitationId, patientName, professionalName, onClose }: LogsModalProps) {
    const { logs, isLoading, error } = useExternalFollowLogs(organizationId, isOpen ? invitationId : null);

    return (
        <Modal isOpen={isOpen} onClose={onClose} title={`Journal d'activité — ${professionalName}`} size="large">
            <div className="external-follow-logs">
                <p className="external-follow-logs__intro">
                    Actions de <strong>{professionalName}</strong> sur le dossier de <strong>{patientName}</strong>.
                    Les simples consultations ne sont pas journalisées.
                </p>
                {isLoading && (
                    <div className="external-follow-logs__empty">
                        <Spinner size="medium" />
                    </div>
                )}
                {!isLoading && error && <Alert variant="error">{error}</Alert>}
                {!isLoading && !error && logs.length === 0 && (
                    <p className="external-follow-logs__empty">Aucune action enregistrée pour le moment.</p>
                )}
                {!isLoading && !error && logs.length > 0 && (
                    <ul className="external-follow-logs__list">
                        {logs.map((log) => (
                            <li key={log.id} className="external-follow-logs__item">
                                <div className="external-follow-logs__item-header">
                                    <span className="external-follow-logs__action">{log.actionLabel ?? log.action}</span>
                                    <time className="external-follow-logs__date">{formatDate(log.createdAt)}</time>
                                </div>
                                {log.detail && <div className="external-follow-logs__detail">{log.detail}</div>}
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </Modal>
    );
}