// services/patientDashboardService.ts
import { getDateFormatLocale, localeCode } from '@/react/i18n/dateLocale';
import { translate } from '@/react/i18n/translations';
import { fetchPatientDossier } from '@/react/features/clinician/patients/services/patientDossierService';
import { fetchPatientTeam } from '@/react/features/patient/appointments/services/patientAppointmentsService';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { PatientDossierData } from '@/react/features/clinician/patients/types';
import {
    PatientDashboardData,
    HealthMetric,
    NextAppointment,
    NextMedication,
    WatchItem,
    UpcomingAppointment,
    ActiveTreatment,
    RecentNote,
} from '../types';

function latest<T>(items: T[]): T | undefined {
    return items[0];
}

function buildMetrics(dossier: PatientDossierData): HealthMetric[] {
    const glucose = latest(dossier.measurements.bloodGlucose);
    const weight = latest(dossier.measurements.weight);
    const hba1c = latest(dossier.measurements.hba1c);
    const bp = latest(dossier.measurements.bloodPressure);
    const loc = localeCode();

    return [
        {
            id: 'glycemie',
            label: translate('Glycémie', loc),
            value: glucose ? String(glucose.value) : '--',
            unit: glucose?.unit ?? 'mg/dL',
            date: glucose?.createdAt,
            tone: glucose && glucose.value > 180 ? 'critical'
                : glucose && glucose.value < 70 ? 'warning' : 'good',
        },
        {
            id: 'tension',
            label: translate('Tension', loc),
            value: bp ? `${bp.systolic}/${bp.diastolic}` : '--',
            unit: 'mmHg',
            date: bp?.createdAt,
            tone: bp && (bp.systolic >= 135 || bp.diastolic >= 85) ? 'warning' : 'good',
        },
        {
            id: 'poids',
            label: translate('Poids', loc),
            value: weight ? String(weight.valueKg) : '--',
            unit: 'kg',
            date: weight?.createdAt,
            tone: 'neutral',
        },
        {
            id: 'hba1c',
            label: translate('HbA1c', loc),
            value: hba1c ? Number(hba1c.valuePercent).toFixed(1) : '--',
            unit: '%',
            date: hba1c?.createdAt,
            tone: hba1c && hba1c.valuePercent > 7 ? 'warning' : 'good',
        },
    ];
}

function buildAppointments(
    dossier: PatientDossierData,
    professionalMap: Map<string, string>,
): {
    nextAppointment: NextAppointment;
    upcomingAppointments: UpcomingAppointment[];
} {
    const now = new Date();
    const future = dossier.appointments
        .filter((a) => new Date(a.scheduledAt) > now)
        .filter((a) => a.status !== 'CANCELLED' && a.status !== 'NO_SHOW')
        .sort((a, b) => new Date(a.scheduledAt).getTime() - new Date(b.scheduledAt).getTime());

    const formatDate = (iso: string) =>
        new Date(iso).toLocaleDateString(getDateFormatLocale(), { day: '2-digit', month: 'long', year: 'numeric' });
    const formatTime = (iso: string) =>
        new Date(iso).toLocaleTimeString(getDateFormatLocale(), { hour: '2-digit', minute: '2-digit' });
    const doctorOf = (professionalId?: string, professionalName?: string) =>
        professionalName?.trim()
        || (professionalId ? professionalMap.get(professionalId) : undefined)
        || 'Non spécifié';

    const upcomingAppointments: UpcomingAppointment[] = future.slice(0, 3).map((a) => ({
        id: a.id,
        date: formatDate(a.scheduledAt),
        time: formatTime(a.scheduledAt),
        doctor: doctorOf(a.professionalId, a.professionalName),
        reason: a.reason,
        status: a.status,
    }));

    const next = upcomingAppointments[0];
    return {
        nextAppointment: next
            ? { date: next.date, time: next.time, doctor: next.doctor }
            : { date: 'Aucun', time: '', doctor: '' },
        upcomingAppointments,
    };
}

function buildTreatments(dossier: PatientDossierData): ActiveTreatment[] {
    const activeIds = new Set(
        dossier.prescriptions.filter((rx) => rx.status === 'ACTIVE').map((rx) => rx.id),
    );
    return dossier.prescriptionItems
        .filter((item) => activeIds.has(item.prescriptionId))
        .map((item) => ({
            id: item.id,
            name: item.medicationName ?? 'Médicament',
            dosage: item.dosage,
            morning: item.morning,
            noon: item.noon,
            evening: item.evening,
            instructions: item.instructions,
        }));
}

function buildNotes(dossier: PatientDossierData): RecentNote[] {
    return [...dossier.notes]
        .sort(
            (a, b) =>
                new Date(b.notedAt ?? b.createdAt).getTime()
                - new Date(a.notedAt ?? a.createdAt).getTime(),
        )
        .slice(0, 5)
        .map((n) => ({
            id: n.id,
            content: n.content,
            authorName: n.authorName,
            date: n.notedAt ?? n.createdAt,
        }));
}

function buildWatchList(
    dossier: PatientDossierData,
    nextAppointment: NextAppointment,
    treatments: ActiveTreatment[],
    metrics: HealthMetric[],
): WatchItem[] {
    const glucose = latest(dossier.measurements.bloodGlucose);
    const hba1c = latest(dossier.measurements.hba1c);
    const bp = latest(dossier.measurements.bloodPressure);

    const watchList: WatchItem[] = [];

    if (glucose && glucose.value > 180) {
        watchList.push({ id: 'high-glucose', message: `Glycémie élevée : ${glucose.value} mg/dL.`, level: 'critical' });
    }
    if (glucose && glucose.value < 70) {
        watchList.push({ id: 'low-glucose', message: `Glycémie basse : ${glucose.value} mg/dL.`, level: 'warning' });
    }
    if (bp && (bp.systolic >= 135 || bp.diastolic >= 85)) {
        watchList.push({
            id: 'high-bp',
            message: `Tension au-dessus de la cible : ${bp.systolic}/${bp.diastolic} mmHg.`,
            level: 'warning',
        });
    }
    if (hba1c && hba1c.valuePercent > 7) {
        watchList.push({
            id: 'hba1c',
            message: `HbA1c à surveiller : ${Number(hba1c.valuePercent).toFixed(1)} % (cible < 7 %).`,
            level: 'warning',
        });
    }
    if (nextAppointment.date !== 'Aucun') {
        watchList.push({
            id: 'appointment',
            message: `Prochain rendez-vous le ${nextAppointment.date} à ${nextAppointment.time}.`,
            level: 'info',
        });
    }
    if (treatments.length === 0) {
        watchList.push({ id: 'no-med', message: 'Aucun traitement actif en cours.', level: 'info' });
    }
    if (metrics.every((m) => m.value === '--')) {
        watchList.push({
            id: 'no-data',
            message: 'Ajoutez vos premières mesures pour activer le suivi.',
            level: 'info',
        });
    }

    return watchList;
}

export async function fetchPatientDashboard(): Promise<PatientDashboardData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    const [dossier, team] = await Promise.all([
        fetchPatientDossier(patientId),
        fetchPatientTeam(patientId).catch(() => []),
    ]);

    const professionalMap = new Map(team.map((p) => [p.id, p.fullName]));

    const metrics = buildMetrics(dossier);
    const { nextAppointment, upcomingAppointments } = buildAppointments(dossier, professionalMap);
    const treatments = buildTreatments(dossier);
    const recentNotes = buildNotes(dossier);
    const nextMedication: NextMedication = treatments[0]
        ? { time: 'Selon ordonnance', name: treatments[0].name }
        : { time: 'Aucune', name: '' };
    const watchList = buildWatchList(dossier, nextAppointment, treatments, metrics);

    return {
        patientName: dossier.profile.fullName || 'Patient',
        metrics,
        nextAppointment,
        nextMedication,
        watchList,
        upcomingAppointments,
        treatments,
        recentNotes,
        hasRecord: dossier.record !== null,
    };
}