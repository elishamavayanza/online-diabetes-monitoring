import { OrganizationReport } from '../types';

export type ReportSectionId =
    | 'demographics'
    | 'healthStatus'
    | 'medicalActivity'
    | 'treatments'
    | 'lifestyle'
    | 'trends';

export interface ReportSectionOption {
    id: ReportSectionId;
    label: string;
    description: string;
}

export const REPORT_SECTIONS: ReportSectionOption[] = [
    {
        id: 'demographics',
        label: 'Patients & démographie',
        description: 'Effectifs, nouveaux patients, répartition par genre et âge',
    },
    {
        id: 'healthStatus',
        label: 'État de santé',
        description: 'Glycémie, HbA1c, tension artérielle, poids et IMC',
    },
    {
        id: 'medicalActivity',
        label: 'Activité médicale',
        description: 'Rendez-vous, diagnostics et dossiers médicaux',
    },
    {
        id: 'treatments',
        label: 'Traitements & observance',
        description: 'Prescriptions actives et taux de prise des médicaments',
    },
    {
        id: 'lifestyle',
        label: 'Nutrition & activité physique',
        description: 'Repas enregistrés et séances d\'activité',
    },
    {
        id: 'trends',
        label: 'Tendances & indicateurs',
        description: 'Suivi des mesures et évolutions dans le temps',
    },
];

export const ALL_SECTION_IDS = REPORT_SECTIONS.map((section) => section.id);

export function buildReportFilename(report: OrganizationReport, sections: ReportSectionId[]): string {
    const slug = report.organizationName
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');

    const sectionSuffix = sections.length === ALL_SECTION_IDS.length
        ? 'complet'
        : sections.join('-');

    return `rapport-${slug}-${report.period.from}_${report.period.to}-${sectionSuffix}.pdf`;
}
