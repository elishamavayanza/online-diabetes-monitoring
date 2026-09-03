import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Treatment } from '../types';

function formatDate(dateStr?: string): string {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

interface TreatmentsListProps {
    treatments: Treatment[];
    isActiveView: boolean;
    onStopTreatment?: (treatment: Treatment) => void;
}

export function TreatmentsList({ treatments, isActiveView, onStopTreatment }: TreatmentsListProps) {
    return (
        <div className="treatments-list">
            {treatments.map((treatment) => (
                <Card key={treatment.id} className="treatment-card">
                    <div className="treatment-card__header">
                        <Badge variant={treatment.categorie === 'INSULINE' ? 'primary' : 'success'}>
                            {treatment.categorie}
                        </Badge>
                        <h3>{treatment.nom}</h3>
                        <span className="treatment-card__dosage">{treatment.dosage}</span>
                    </div>

                    <div className="treatment-card__horaires">
                        {treatment.horaires.map((h, idx) => (
                            <span key={idx} className="treatment-card__horaire">{h}</span>
                        ))}
                    </div>

                    {treatment.instructions && (
                        <p className="treatment-card__instructions">
                            <strong>Instructions :</strong> {treatment.instructions}
                        </p>
                    )}

                    {treatment.quantity && (
                        <p className="treatment-card__quantity">
                            <strong>Quantité :</strong> {treatment.quantity}
                        </p>
                    )}

                    {(treatment.startDate || treatment.endDate) && (
                        <div className="treatment-card__dates">
                            {treatment.startDate && <span><strong>Début :</strong> {formatDate(treatment.startDate)}</span>}
                            {treatment.endDate && <span><strong>Fin :</strong> {formatDate(treatment.endDate)}</span>}
                        </div>
                    )}

                    {treatment.prescriberName && (
                        <p className="treatment-card__prescriber">
                            <strong>Prescrit par :</strong> {treatment.prescriberName}
                        </p>
                    )}

                    {/* Statut pour l'historique : badge distinct */}
                    {!isActiveView && treatment.status && (
                        <div className="treatment-card__status">
                            {treatment.status === 'CANCELLED' ? (
                                <Badge variant="error">Arrêté</Badge>
                            ) : treatment.status === 'COMPLETED' ? (
                                <Badge variant="success">Terminé</Badge>
                            ) : (
                                <Badge variant="warning">Inactif</Badge>
                            )}
                        </div>
                    )}

                    {/* Motif d'arrêt pour les traitements historiques */}
                    {!isActiveView && treatment.stopReason && (
                        <div className="treatment-card__stop-reason">
                            <strong>Motif d’arrêt :</strong> {treatment.stopReason}
                        </div>
                    )}

                    {/* Bouton Arrêter uniquement pour les actifs */}
                    {isActiveView && onStopTreatment && (
                        <div className="treatment-card__actions">
                            <Button variant="danger" size="small" onClick={() => onStopTreatment(treatment)}>
                                Arrêter
                            </Button>
                        </div>
                    )}
                </Card>
            ))}
        </div>
    );
}
