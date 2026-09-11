import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge/Badge';
import { useI18n } from '@/react/i18n/I18nContext';
import { FollowUpPatient } from '../types';

interface FollowUpListProps {
    patients: FollowUpPatient[];
}

export function FollowUpList({ patients }: FollowUpListProps) {
    const { t } = useI18n();
    return (
        <Card className="follow-up">
            <h2 className="section-title">{t('Patients sans rendez-vous planifié')}</h2>
            {patients.length === 0 ? (
                <p className="follow-up__empty">{t('Tous vos patients actifs ont un rendez-vous à venir.')}</p>
            ) : (
                <ul className="follow-up__list">
                    {patients.map((patient) => (
                        <li key={patient.id} className="follow-up__item">
                            <span className="follow-up__name">{patient.name}</span>
                            <span className="follow-up__last-visit">
                                {patient.lastVisit === 'Jamais consulté' ? (
                                    <Badge variant="warning">{t('Jamais consulté')}</Badge>
                                ) : (
                                    <>{t('Dernier RDV : {{ date }}', { date: patient.lastVisit })}</>
                                )}
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}