import { FollowUpReportElementId } from '../types/followUpReport';

export interface FollowUpReportElementOption {
    id: FollowUpReportElementId;
    label: string;
    description: string;
}

export const FOLLOW_UP_REPORT_ELEMENTS: FollowUpReportElementOption[] = [
    {
        id: 'glucose',
        label: 'Glycémie',
        description: 'Moyenne, min/max et évolution de la glycémie',
    },
    {
        id: 'hba1c',
        label: 'HbA1c',
        description: 'Évolution de l\'HbA1c sur la période',
    },
    {
        id: 'blood_pressure',
        label: 'Tension artérielle',
        description: 'Tension systolique et diastolique',
    },
    {
        id: 'weight',
        label: 'Poids / IMC',
        description: 'Évolution du poids et de l\'indice de masse corporelle',
    },
    {
        id: 'treatment',
        label: 'Traitement / observance',
        description: 'Prescriptions actives et taux d\'observance',
    },
    {
        id: 'physical_activity',
        label: 'Activité physique',
        description: 'Durée et fréquence des activités enregistrées',
    },
    {
        id: 'nutrition',
        label: 'Repas / nutrition',
        description: 'Repas enregistrés par type',
    },
    {
        id: 'laboratory',
        label: 'Résultats de laboratoire',
        description: 'Examens de laboratoire de la période',
    },
];

export const ALL_FOLLOW_UP_ELEMENT_IDS = FOLLOW_UP_REPORT_ELEMENTS.map((element) => element.id);

export function buildFollowUpReportFilename(
    patientName: string,
    from: string,
    to: string,
): string {
    const slug = patientName
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    return `rapport-suivi-${slug}-${from}-${to}.pdf`;
}

export function getDefaultFollowUpPeriod(): { from: string; to: string } {
    const today = new Date();
    const from = new Date(today);
    from.setDate(today.getDate() - 29);

    const format = (date: Date) => date.toISOString().slice(0, 10);

    return {
        from: format(from),
        to: format(today),
    };
}
