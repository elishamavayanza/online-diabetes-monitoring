import {ProfessionalOption, AvailableSlot, BookingFormData} from '../types';

export async function fetchProfessionals(): Promise<ProfessionalOption[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return [
        { id: 'p1', nom: 'Dr. Jean Dupont', specialite: 'Endocrinologie' },
        { id: 'p2', nom: 'Nutritionniste Sarah K.', specialite: 'Nutrition' },
        { id: 'p3', nom: 'Dr. Alice Martin', specialite: 'Médecine générale' },
    ];
}

export async function fetchAvailableSlots(date: string, professionnelId: string): Promise<AvailableSlot[]> {
    await new Promise((resolve) => setTimeout(resolve, 300));
    // Simulation de créneaux
    return [
        { time: '08:00', disponible: true },
        { time: '09:00', disponible: true },
        { time: '10:00', disponible: false },
        { time: '11:00', disponible: true },
        { time: '14:00', disponible: true },
    ];
}

export async function submitBooking(data: BookingFormData): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    console.log('Rendez-vous pris', data);
}
