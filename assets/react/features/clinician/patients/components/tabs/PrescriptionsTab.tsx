import { useMemo, useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Collapsible } from '@/react/components/UI/Collapsible';
import { ConfirmDialog } from '@/react/components/UI/ConfirmDialog';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { useI18n } from '@/react/i18n/I18nContext';
import { PatientPrescription, PrescriptionItem } from '../../types';
import {
    deletePrescriptionItem,
} from '../../services/dossierActionsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { isRecordCreator } from '../../utils/ownershipUtils';
import { RecordAuthor } from '../RecordAuthor';
import {updatePrescription} from "@/react/features/clinician/patients/services/medicalRecordService";
import {PrescriptionEditModal} from "@/react/features/clinician/patients/components/modals/prescription/PrescriptionEditModal";
import {
    PrescriptionItemEditModal
} from "@/react/features/clinician/patients/components/modals/prescription/PrescriptionItemEditModal";

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

    const { showToast } = useToast();
    const { t } = useI18n();
    const { prescriptions, prescriptionItems, prescriptionVersions } = data;

    const [deleteItemId, setDeleteItemId] = useState<string | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const [editingRx, setEditingRx] = useState<PatientPrescription | null>(null);
    const [editingItem, setEditingItem] = useState<PrescriptionItem | null>(null);

    const filtered = prescriptions.filter((rx) => {
        const date = rx.startDate ?? rx.endDate;
        return date ? isInPeriod(date, period, selectedDate) : !selectedDate;
    });

    const itemsByPrescription = useMemo(() => {
        const map = new Map<string, PrescriptionItem[]>();
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
            showToast({ type: 'success', message: t('Médicament retiré.') });
            reload();
            setDeleteItemId(null);
        } catch (err) {
            const message = err instanceof Error ? err.message : t('Erreur lors de la suppression.');
            showToast({ type: 'error', message });
        } finally {
            setIsDeleting(false);
        }
    };

    const handleActivatePrescription = async (rx: PatientPrescription) => {
        const prescriberId = getCurrentUserIdFromToken();
        const organizationId = data.profile.organizationId;
        if (!prescriberId || !organizationId) {
            showToast({ type: 'error', message: t('Prescripteur ou organisation introuvable.') });
            return;
        }
        try {
            await updatePrescription(rx.id, {
                patientId: data.profile.id,
                prescriberId,
                organizationId,
                startDate: rx.startDate || new Date().toISOString(),
                endDate: rx.endDate,
                status: 'ACTIVE',
                notes: rx.notes || undefined,
            });
            showToast({ type: 'success', message: t('Prescription activée.') });
            reload();
        } catch (err) {
            const message = err instanceof Error ? err.message : t("Erreur lors de l'activation.");
            showToast({ type: 'error', message });
        }
    };

    const handleEditRx = (rx: PatientPrescription) => setEditingRx(rx);
    const handleEditItem = (item: PrescriptionItem) => setEditingItem(item);

    return (
        <div className="patient-dossier-tab patient-dossier-tab--prescriptions">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">{t('Ordonnances, médicaments et historique des versions.')}</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={openPrescriptionModal}>
                        {t('+ Nouvelle prescription')}
                    </Button>
                )}
            </div>

            {filtered.length === 0 ? (
                <Card><p>{t('Aucune prescription sur la période sélectionnée.')}</p></Card>
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
                                {rx.startDate && <p><strong>{t('Début :')}</strong> {formatDisplayDate(rx.startDate)}</p>}
                                {rx.endDate && <p><strong>{t('Fin :')}</strong> {formatDisplayDate(rx.endDate)}</p>}
                                {rx.notes && <p><strong>{t('Notes :')}</strong> {rx.notes}</p>}
                                <RecordAuthor record={rx} />

                                {!isReadOnly && isRecordCreator(rx) && (
                                    <div className="patient-dossier-tab__item-actions">
                                        {rx.status === 'DRAFT' && (
                                            <Button
                                                variant="success"
                                                size="small"
                                                onClick={() => handleActivatePrescription(rx)}
                                            >
                                                {t('Activer')}
                                            </Button>
                                        )}
                                        <Button variant="secondary" size="small" onClick={() => handleEditRx(rx)}>
                                            {t('Modifier')}
                                        </Button>
                                        {rx.status === 'ACTIVE' && (
                                            <Button variant="secondary" size="small" onClick={() => openPrescriptionItemModal(rx)}>
                                                {t('+ Médicament')}
                                            </Button>
                                        )}
                                        <Button variant="secondary" size="small" onClick={() => openPrescriptionVersionModal(rx)}>
                                            {t('Archiver version')}
                                        </Button>
                                    </div>
                                )}

                                <Collapsible
                                    trigger={<span className="patient-dossier-tab__collapsible-trigger">{t('Médicaments ({{ count }})', { count: items.length })}</span>}
                                >
                                    {items.length === 0 ? (
                                        <p>{t('Aucun médicament prescrit.')}</p>
                                    ) : (
                                        <ul className="patient-dossier-tab__list">
                                            {items.map((item) => (
                                                <li key={item.id} className="patient-dossier-tab__prescription-item">
                                                    <div>
                                                        {item.medicationName && (
                                                            <>
                                                                <strong>{item.medicationName}</strong>
                                                                {item.medicationCategory === 'INSULIN' && (
                                                                    <Badge variant="info" size="small" pill className="patient-dossier-tab__insulin-badge">
                                                                        {t('Insuline')}
                                                                    </Badge>
                                                                )}
                                                                <br />
                                                            </>
                                                        )}
                                                        <strong>{item.dosage}</strong> — {t('Qté: {{ quantity }}', { quantity: item.quantity })}
                                                        <br />
                                                        <small>{[
                                                            item.morning ? t('Matin') : '',
                                                            item.noon ? t('Midi') : '',
                                                            item.evening ? t('Soir') : '',
                                                        ].filter(Boolean).join(', ')}</small>
                                                        {item.instructions && <p><em>{item.instructions}</em></p>}
                                                        <RecordAuthor record={item} />
                                                    </div>
                                                    {!isReadOnly && isRecordCreator(item) && (
                                                        <div className="patient-dossier-tab__item-actions">
                                                            <Button variant="secondary" size="small" onClick={() => handleEditItem(item)}>
                                                                {t('Modifier')}
                                                            </Button>
                                                            <Button variant="danger" size="small" onClick={() => setDeleteItemId(item.id)}>
                                                                {t('Retirer')}
                                                            </Button>
                                                        </div>
                                                    )}
                                                </li>
                                            ))}
                                        </ul>
                                    )}
                                </Collapsible>

                                <Collapsible
                                    trigger={<span className="patient-dossier-tab__collapsible-trigger">{t('Historique des versions ({{ count }})', { count: versions.length })}</span>}
                                >
                                    {versions.length === 0 ? (
                                        <p>{t('Aucune version archivée.')}</p>
                                    ) : (
                                        <ul className="patient-dossier-tab__list">
                                            {versions.map((version) => (
                                                <li key={version.id}>
                                                    <strong>v{version.versionNumber}</strong>
                                                    {' — '}
                                                    {formatDisplayDateTime(version.modifiedAt)}
                                                    <RecordAuthor record={version} label="Modifié par" />
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
                title={t('Retirer le médicament')}
                message={t('Voulez-vous retirer ce médicament de la prescription ?')}
                confirmLabel={isDeleting ? t('Suppression...') : t('Retirer')}
                cancelLabel={t('Annuler')}
            />

            {editingRx && (
                <PrescriptionEditModal
                    isOpen={!!editingRx}
                    onClose={() => setEditingRx(null)}
                    data={data}
                    prescription={editingRx}
                    onSuccess={reload}
                />
            )}

            {editingItem && (
                <PrescriptionItemEditModal
                    isOpen={!!editingItem}
                    onClose={() => setEditingItem(null)}
                    item={editingItem}
                    onSuccess={reload}
                />
            )}
        </div>
    );
}
