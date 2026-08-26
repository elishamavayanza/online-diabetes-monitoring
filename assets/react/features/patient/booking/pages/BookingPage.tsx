import { useState } from 'react';
import { BookingForm } from '../components/BookingForm';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/booking/_booking.scss';

export function BookingPage() {
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    return (
        <div className="booking-page">
            <div className="booking-page__header">
                <h1>Prendre rendez-vous</h1>
                <p>Réservez une consultation</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <BookingForm />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Choisissez un professionnel, une date, une heure et un motif.</p>
                </Modal>
            )}
        </div>
    );
}
