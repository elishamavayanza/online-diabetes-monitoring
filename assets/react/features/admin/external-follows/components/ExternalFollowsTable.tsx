import React, { useState } from 'react';
import { Button } from '@/react/components/UI/Button';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { ExternalFollowInvitation } from '../types/types';
import { StatusBadge } from './StatusBadge';
import { useRevokeExternalFollow } from '../hooks/useRevokeExternalFollow';

interface ExternalFollowsTableProps {
    invitations: ExternalFollowInvitation[];
    organizationId: string;
    isLoading: boolean;
    error: string | null;
    onRefresh: () => void;
    onRenew: (invitation: ExternalFollowInvitation) => void;
    onOpenLogs: (invitation: ExternalFollowInvitation) => void;
}

function formatDate(value: string | null): string {
    if (!value) return '—';
    return new Date(value).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

export function ExternalFollowsTable({
    invitations,
    organizationId,
    isLoading,
    error,
    onRefresh,
    onRenew,
    onOpenLogs,
}: ExternalFollowsTableProps) {
    const { submit: revoke, isSubmitting: isRevoking } = useRevokeExternalFollow(organizationId, { onSuccess: onRefresh });
    const [revokeTarget, setRevokeTarget] = useState<ExternalFollowInvitation | null>(null);

    const handleRevokeConfirm = async () => {
        if (!revokeTarget) return;
        await revoke(revokeTarget.id);
        setRevokeTarget(null);
    };

    if (isLoading) {
        return (
            <div className="external-follows-table__empty">
                <Spinner size="medium" />
            </div>
        );
    }

    if (error) {
        return (
            <div className="external-follows-table__empty">
                <Alert variant="error">{error}</Alert>
            </div>
        );
    }

    if (invitations.length === 0) {
        return (
            <div className="external-follows-table__empty">
                <p>Aucune invitation pour le moment.</p>
                <p className="external-follows-table__empty-hint">
                    Utilisez « Nouvelle invitation » pour partager le suivi d'un patient.
                </p>
            </div>
        );
    }

    return (
        <>
            <div className="external-follows-table__wrapper">
                <table className="external-follows-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Professionnel</th>
                            <th>Statut</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Invité par</th>
                            <th className="external-follows-table__actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {invitations.map((inv) => {
                            const isActive = inv.status === 'ACCEPTED' || inv.status === 'PENDING';
                            const isExpired = inv.status === 'EXPIRED';
                            return (
                                <tr key={inv.id}>
                                    <td>{inv.patientName}</td>
                                    <td>
                                        {inv.professionalName}
                                        <span className="external-follows-table__email">{inv.email}</span>
                                    </td>
                                    <td>
                                        <StatusBadge status={inv.status} />
                                        {inv.status === 'CLOSED_BY_PROFESSIONAL' && inv.closureReason && (
                                            <span
                                                className="external-follows-table__closure-reason"
                                                title={inv.closureReason}
                                            >
                                                Motif : {inv.closureReason}
                                            </span>
                                        )}
                                    </td>
                                    <td>{formatDate(inv.startDate)}</td>
                                    <td>{formatDate(inv.endDate)}</td>
                                    <td>{inv.invitedByName}</td>
                                    <td className="external-follows-table__actions-col">
                                        <div className="external-follows-table__actions">
                                            <Button
                                                variant="ghost"
                                                size="small"
                                                onClick={() => onOpenLogs(inv)}
                                                title="Voir le journal d’activité"
                                            >
                                                Journal
                                            </Button>
                                            {isActive && (
                                                <Button variant="ghost" size="small" onClick={() => onRenew(inv)}>
                                                    Renouveler
                                                </Button>
                                            )}
                                            {isActive && (
                                                <Button
                                                    variant="ghost"
                                                    size="small"
                                                    className="external-follows-table__revoke"
                                                    onClick={() => setRevokeTarget(inv)}
                                                >
                                                    Couper l’accès
                                                </Button>
                                            )}
                                            {isExpired && (
                                                <span className="external-follows-table__expired-note">Délai écoulé</span>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            );
                        })}
                    </tbody>
                </table>
            </div>
            <ConfirmDialog
                isOpen={revokeTarget !== null}
                onClose={() => setRevokeTarget(null)}
                onConfirm={handleRevokeConfirm}
                title="Couper l'accès"
                message={
                    revokeTarget
                        ? `L'accès de ${revokeTarget.professionalName} au dossier de ${revokeTarget.patientName} sera coupé immédiatement. Cette action est irréversible.`
                        : ''
                }
                confirmLabel={isRevoking ? 'Coupure...' : 'Couper l’accès'}
                cancelLabel="Annuler"
            />
        </>
    );
}