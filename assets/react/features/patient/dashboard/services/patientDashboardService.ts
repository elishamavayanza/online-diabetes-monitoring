import { PatientDashboardData } from '../types';

export async function fetchPatientDashboard(): Promise<PatientDashboardData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        patientName: 'Jean',
        metrics: [
            { id: 'glycemie', label: 'Glycémie', value: '120', unit: 'mg/dL' },
            { id: 'poids', label: 'Poids', value: '72', unit: 'kg' },
            { id: 'hba1c', label: 'HbA1c', value: '6,8', unit: '%' },
        ],
        nextAppointment: {
            date: "Aujourd'hui",
            time: '14:00',
            doctor: 'Dr. Dupont',
        },
        nextMedication: {
            time: '18:00',
            name: 'Insuline',
        },
        watchList: [
            { id: '1', message: 'Mesure de glycémie prévue' },
            { id: '2', message: 'Rendez-vous demain à 10:00' },
            { id: '3', message: 'Prise de médicament à 18:00' },
        ],
    };
}
