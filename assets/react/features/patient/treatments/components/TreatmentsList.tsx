import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Treatment } from '../types';

// Formate une date ISO en format lisible
function formatDate(dateStr?: string): string {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

interface TreatmentsListProps {
    treatments: Treatment[];
}

export function TreatmentsList({ treatments }: TreatmentsListProps) {
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

                    {/* Horaires */}
                    <div className="treatment-card__horaires">
                        {treatment.horaires.map((h, idx) => (
                            <span key={idx} className="treatment-card__horaire">{h}</span>
                        ))}
                    </div>

                    {/* Instructions */}
                    {treatment.instructions && (
                        <p className="treatment-card__instructions">
                            <strong>Instructions :</strong> {treatment.instructions}
                        </p>
                    )}

                    {/* Quantité */}
                    {treatment.quantity && (
                        <p className="treatment-card__quantity">
                            <strong>Quantité :</strong> {treatment.quantity}
                        </p>
                    )}

                    {/* Dates de prescription */}
                    {(treatment.startDate || treatment.endDate) && (
                        <div className="treatment-card__dates">
                            {treatment.startDate && (
                                <span>
                                    <strong>Début :</strong> {formatDate(treatment.startDate)}
                                </span>
                            )}
                            {treatment.endDate && (
                                <span>
                                    <strong>Fin :</strong> {formatDate(treatment.endDate)}
                                </span>
                            )}
                        </div>
                    )}

                    {/* Prescripteur */}
                    {treatment.prescriberName && (
                        <p className="treatment-card__prescriber">
                            <strong>Prescrit par :</strong> {treatment.prescriberName}
                        </p>
                    )}
                </Card>
            ))}
        </div>
    );
}
