import { useState } from 'react';
import { BookingForm } from '../components/BookingForm';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/patient/booking/_booking.scss';

export function BookingPage() {
    const { t } = useI18n();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    return (
        <div className="booking-page">
            <div className="booking-page__header">
                <h1>{t('Prendre rendez-vous')}</h1>
                <p>{t('Réservez une consultation')}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>
            <BookingForm />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Choisissez un professionnel, une date, une heure et un motif.')}</p>
                </Modal>
            )}
        </div>
    );
}
