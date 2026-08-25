import { useEffect, useState } from 'react';
import { fetchProfessionals, fetchAvailableSlots, submitBooking } from '../services/bookingService';
import { ProfessionalOption, AvailableSlot, BookingFormData } from '../types';

export function useBooking() {
    const [professionals, setProfessionals] = useState<ProfessionalOption[]>([]);
    const [slots, setSlots] = useState<AvailableSlot[]>([]);
    const [form, setForm] = useState<BookingFormData>({
        professionnelId: '',
        date: '',
        heure: '',
        motif: '',
    });
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState(false);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchProfessionals();
                setProfessionals(data);
            } catch (err) {
                setError('Impossible de charger les professionnels.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    useEffect(() => {
        if (form.professionnelId && form.date) {
            const loadSlots = async () => {
                const data = await fetchAvailableSlots(form.date, form.professionnelId);
                setSlots(data);
            };
            loadSlots();
        }
    }, [form.professionnelId, form.date]);

    const updateForm = (field: keyof BookingFormData, value: string) => {
        setForm((prev) => ({ ...prev, [field]: value }));
    };

    const submit = async () => {
        setIsSubmitting(true);
        setError(null);
        try {
            await submitBooking(form);
            setSuccess(true);
        } catch (err) {
            setError('Erreur lors de la prise de rendez-vous.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        professionals,
        slots,
        form,
        updateForm,
        submit,
        isLoading,
        isSubmitting,
        error,
        success,
    };
}
