export interface HealthMetric {
    id: string;
    label: string;
    value: string;
    unit: string;
}

export interface NextAppointment {
    date: string;
    time: string;
    doctor: string;
}

export interface NextMedication {
    time: string;
    name: string;
}

export interface WatchItem {
    id: string;
    message: string;
}

export interface PatientDashboardData {
    patientName: string;
    metrics: HealthMetric[];
    nextAppointment: NextAppointment;
    nextMedication: NextMedication;
    watchList: WatchItem[];
}
