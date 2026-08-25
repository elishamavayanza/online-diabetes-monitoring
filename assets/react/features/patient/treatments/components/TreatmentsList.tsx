import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Treatment } from '../types';

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
                    <div className="treatment-card__horaires">
                        {treatment.horaires.map((h, idx) => (
                            <span key={idx} className="treatment-card__horaire">{h}</span>
                        ))}
                    </div>
                </Card>
            ))}
        </div>
    );
}
