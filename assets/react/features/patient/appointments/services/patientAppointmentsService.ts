import { PatientAppointment } from '../types';

export async function fetchPatientAppointments(): Promise<PatientAppointment[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        { id: '1', date: "Aujourd'hui", heure: '14:00', professionnel: 'Dr. Dupont', motif: 'Consultation', statut: 'Confirmé' },
        { id: '2', date: '25 août', heure: '10:00', professionnel: 'Nutritionniste', motif: 'Suivi nutritionnel', statut: 'En attente' },
        { id: '3', date: '20 août', heure: '09:00', professionnel: 'Dr. Martin', motif: 'Consultation', statut: 'Terminé' },
    ];
}
