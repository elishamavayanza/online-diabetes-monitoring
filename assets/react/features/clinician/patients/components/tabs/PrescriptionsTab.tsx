import { useMemo, useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Collapsible } from '@/react/components/UI/Collapsible';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';
import { formatSchedule } from '../../utils/labelUtils';
import { deletePrescriptionItem } from '../../services/dossierActionsService';

export function PrescriptionsTab() {
    const {
        data,
        period,
        selectedDate,
        isReadOnly,
        openPrescriptionModal,
        openPrescriptionItemModal,
        openPrescriptionVersionModal,
        reload,
    } = usePatientDossierContext();

    const { prescriptions, prescriptionItems, prescriptionVersions } = data;
    const [deleteItemId, setDeleteItemId] = useState<string | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const filtered = prescriptions.filter((rx) => {
        const date = rx.startDate ?? rx.endDate;
        return date ? isInPeriod(date, period, selectedDate) : !selectedDate;
    });

    const itemsByPrescription = useMemo(() => {
        const map = new Map<string, typeof prescriptionItems>();
        prescriptionItems.forEach((item) => {
            const list = map.get(item.prescriptionId) ?? [];
            list.push(item);
            map.set(item.prescriptionId, list);
        });
        return map;
    }, [prescriptionItems]);

    const versionsByPrescription = useMemo(() => {
        const map = new Map<string, typeof prescriptionVersions>();
        prescriptionVersions.forEach((version) => {
            const list = map.get(version.prescriptionId) ?? [];
            list.push(version);
            map.set(version.prescriptionId, list);
        });
        return map;
    }, [prescriptionVersions]);

    const handleDeleteItem = async () => {
        if (!deleteItemId) return;
        setIsDeleting(true);
        try {
            await deletePrescriptionItem(deleteItemId);
            reload();
            setDeleteItemId(null);
        } finally {
            setIsDeleting(false);
        }
    };

    return (
        <div className="patient-dossier-tab patient-dossier-tab--prescriptions">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">Ordonnances, médicaments et historique des versions.</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={openPrescriptionModal}>
                        + Nouvelle prescription
                    </Button>
                )}
            </div>

            {filtered.length === 0 ? (
                <Card><p>Aucune prescription sur la période sélectionnée.</p></Card>
            ) : (
                <div className="patient-dossier-tab__prescriptions">
                    {filtered.map((rx) => {
                        const items = itemsByPrescription.get(rx.id) ?? [];
                        const versions = (versionsByPrescription.get(rx.id) ?? [])
                            .sort((a, b) => b.versionNumber - a.versionNumber);

                        return (
                            <Card key={rx.id} className="patient-dossier-tab__prescription-card">
                                <div className="patient-dossier-tab__card-header">
                                    <h3>Prescription #{rx.id.slice(0, 8)}</h3>
                                    <Badge variant={rx.status === 'ACTIVE' ? 'success' : 'warning'}>{rx.status}</Badge>
                                </div>
                                {rx.startDate && <p><strong>Début :</strong> {formatDisplayDate(rx.startDate)}</p>}
                                {rx.endDate && <p><strong>Fin :</strong> {formatDisplayDate(rx.endDate)}</p>}
                                {rx.notes && <p><strong>Notes :</strong> {rx.notes}</p>}

                                {!isReadOnly && (
                                    <div className="patient-dossier-tab__item-actions">
                                        <Button variant="secondary" size="small" onClick={() => openPrescriptionItemModal(rx)}>
                                            + Médicament
                                        </Button>
                                        <Button variant="secondary" size="small" onClick={() => openPrescriptionVersionModal(rx)}>
                                            Archiver version
                                        </Button>
                                    </div>
                                )}

                                <Collapsible
                                    trigger={<span className="patient-dossier-tab__collapsible-trigger">Médicaments ({items.length})</span>}
                                >
                                    {items.length === 0 ? (
                                        <p>Aucun médicament prescrit.</p>
                                    ) : (
                                        <ul className="patient-dossier-tab__list">
                                            {items.map((item) => (
                                                <li key={item.id} className="patient-dossier-tab__prescription-item">
                                                    <div>
                                                        <strong>{item.dosage}</strong> — Qté: {item.quantity}
                                                        <br />
                                                        <small>{formatSchedule(item.morning, item.noon, item.evening)}</small>
                                                        {item.instructions && <p><em>{item.instructions}</em></p>}
                                                    </div>
                                                    {!isReadOnly && (
                                                        <Button variant="danger" size="small" onClick={() => setDeleteItemId(item.id)}>
                                                            Retirer
                                                        </Button>
                                                    )}
                                                </li>
                                            ))}
                                        </ul>
                                    )}
                                </Collapsible>

                                <Collapsible
                                    trigger={<span className="patient-dossier-tab__collapsible-trigger">Historique des versions ({versions.length})</span>}
                                >
                                    {versions.length === 0 ? (
                                        <p>Aucune version archivée.</p>
                                    ) : (
                                        <ul className="patient-dossier-tab__list">
                                            {versions.map((version) => (
                                                <li key={version.id}>
                                                    <strong>v{version.versionNumber}</strong>
                                                    {' — '}
                                                    {formatDisplayDateTime(version.modifiedAt)}
                                                    {version.changesSummary && (
                                                        <p><em>{version.changesSummary}</em></p>
                                                    )}
                                                </li>
                                            ))}
                                        </ul>
                                    )}
                                </Collapsible>
                            </Card>
                        );
                    })}
                </div>
            )}

            <ConfirmDialog
                isOpen={!!deleteItemId}
                onClose={() => setDeleteItemId(null)}
                onConfirm={handleDeleteItem}
                title="Retirer le médicament"
                message="Voulez-vous retirer ce médicament de la prescription ?"
                confirmLabel={isDeleting ? 'Suppression...' : 'Retirer'}
                cancelLabel="Annuler"
            />
        </div>
    );
}
