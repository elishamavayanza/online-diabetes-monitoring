import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDate, isInPeriod } from '../../utils/dossierUtils';

export function PrescriptionsTab() {
    const { data, period, selectedDate, isReadOnly, openPrescriptionModal } = usePatientDossierContext();
    const { prescriptions } = data;

    const filtered = prescriptions.filter((rx) => {
        const date = rx.startDate ?? rx.endDate;
        return date ? isInPeriod(date, period, selectedDate) : !selectedDate;
    });

    return (
        <div className="patient-dossier-tab patient-dossier-tab--prescriptions">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">Ordonnances et traitements prescrits.</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={openPrescriptionModal}>
                        + Nouvelle prescription
                    </Button>
                )}
            </div>

            {filtered.length === 0 ? (
                <Card><p>Aucune prescription sur la période sélectionnée.</p></Card>
            ) : (
                <div className="patient-dossier-tab__grid">
                    {filtered.map((rx) => (
                        <Card key={rx.id}>
                            <div className="patient-dossier-tab__card-header">
                                <h3>Prescription #{rx.id}</h3>
                                <Badge variant={rx.status === 'ACTIVE' ? 'success' : 'warning'}>{rx.status}</Badge>
                            </div>
                            {rx.startDate && <p><strong>Début :</strong> {formatDisplayDate(rx.startDate)}</p>}
                            {rx.endDate && <p><strong>Fin :</strong> {formatDisplayDate(rx.endDate)}</p>}
                            {rx.notes && <p><strong>Notes :</strong> {rx.notes}</p>}
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}
