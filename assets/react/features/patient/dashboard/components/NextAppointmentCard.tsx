import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';
import { NextAppointment, NextMedication } from '../types';

interface NextAppointmentCardProps {
    appointment: NextAppointment;
    medication: NextMedication;
}

export function NextAppointmentCard({ appointment, medication }: NextAppointmentCardProps) {
    const { t } = useI18n();

    return (
        <Card className="next-appointment-card">
            <div className="next-appointment-card__item">
                <span className="label">{t('Prochain rendez-vous')}</span>
                <span className="value">{appointment.date} — {appointment.time}</span>
                <span className="sub">{appointment.doctor}</span>
            </div>
            <div className="next-appointment-card__item">
                <span className="label">{t('Prochaine prise')}</span>
                <span className="value">{medication.time} — {medication.name}</span>
            </div>
        </Card>
    );
}
