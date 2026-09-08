import { Card } from '@/react/components/UI/Card';
import { ActiveTreatment } from '../types';

interface ActiveTreatmentsCardProps {
    treatments: ActiveTreatment[];
}

function ScheduleDot({ active }: { active: boolean }) {
    return <span className={`treatment-schedule ${active ? 'treatment-schedule--active' : ''}`} />;
}

export function ActiveTreatmentsCard({ treatments }: ActiveTreatmentsCardProps) {
    if (treatments.length === 0) {
        return (
            <Card className="active-treatments-card">
                <h3>Traitements en cours</h3>
                <p className="active-treatments-card__empty">Aucun traitement actif.</p>
            </Card>
        );
    }

    return (
        <Card className="active-treatments-card">
            <h3>Traitements en cours</h3>
            <ul>
                {treatments.map((treatment) => (
                    <li key={treatment.id} className="active-treatments-card__item">
                        <div className="active-treatments-card__heading">
                            <strong>{treatment.name}</strong>
                            {treatment.dosage && <span className="active-treatments-card__dosage">{treatment.dosage}</span>}
                        </div>
                        <div className="active-treatments-card__schedule">
                            <span><ScheduleDot active={treatment.morning} /> Matin</span>
                            <span><ScheduleDot active={treatment.noon} /> Midi</span>
                            <span><ScheduleDot active={treatment.evening} /> Soir</span>
                        </div>
                        {treatment.instructions && (
                            <p className="active-treatments-card__instructions">{treatment.instructions}</p>
                        )}
                    </li>
                ))}
            </ul>
        </Card>
    );
}