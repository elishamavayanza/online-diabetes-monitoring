import { BookingForm } from '../components/BookingForm';
import '@/styles/pages/patient/booking/_booking.scss';

export function BookingPage() {
    return (
        <div className="booking-page">
            <div className="booking-page__header">
                <h1>Prendre rendez-vous</h1>
                <p>Réservez une consultation</p>
            </div>
            <BookingForm />
        </div>
    );
}
