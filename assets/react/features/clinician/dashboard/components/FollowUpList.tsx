import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge/Badge';
import { FollowUpPatient } from '../types';

interface FollowUpListProps {
    patients: FollowUpPatient[];
}

export function FollowUpList({ patients }: FollowUpListProps) {
    return (
        <Card className="follow-up">
            <h2 className="section-title">Patients sans rendez-vous planifié</h2>
            {patients.length === 0 ? (
                <p className="follow-up__empty">Tous vos patients actifs ont un rendez-vous à venir.</p>
            ) : (
                <ul className="follow-up__list">
                    {patients.map((patient) => (
                        <li key={patient.id} className="follow-up__item">
                            <span className="follow-up__name">{patient.name}</span>
                            <span className="follow-up__last-visit">
                                {patient.lastVisit === 'Jamais consulté' ? (
                                    <Badge variant="warning">{patient.lastVisit}</Badge>
                                ) : (
                                    <>Dernier RDV : {patient.lastVisit}</>
                                )}
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}